<?php

namespace isrdxv\practice;

use isrdxv\practice\Practice;
use isrdxv\practice\listener\PracticeListener;
use isrdxv\practice\command\{
    HubCommand,
    BanCommand,
    InfoCommand,
    ArenaCommand,
    DuelCommand,
    KitCommand,
    MaintenanceCommand
};
use isrdxv\practice\task\BroadcastTask;
use isrdxv\practice\utils\Time;
use isrdxv\practice\manager\{
    ArenaManager,
    TaskManager,
    ItemManager,
    KitManager,
    SessionManager
};

use pocketmine\plugin\PluginBase;
use pocketmine\utils\{
  	TextFormat,
    SingletonTrait
};
use pocketmine\world\World;

use poggit\libasynql\{
    libasynql, 
    DataConnector
};

use CortexPE\Commando\PacketHooker;
use isrdxv\practice\handler\DuelHandler;
use isrdxv\practice\handler\QueueHandler;
use isrdxv\practice\listener\DuelListener;

use DateTime;
use DateTimeZone;

class PracticeLoader extends PluginBase
{
    use SingletonTrait;

    private $database;
    
    private DateTime $seasonStart;

    private DateTime $seasonEnd;

    function onLoad(): void
    {
        self::setInstance($this);
        $this->saveDefaultConfig();
        $this->getServer()->getNetwork()->setName(Practice::SERVER_MOTD);
    }

    function onEnable(): void
    {
    	//RECORDER
        if (!PacketHooker::isRegistered()) {
          PacketHooker::register($this);
        }
        //DATABASE
        $this->database = libasynql::create($this, $this->getConfig()->get("database"), [
            "sqlite" => "sqlite.sql",
            "mysql" => "mysql.sql"
        ]);
        $this->database->executeGeneric("practice.init.user");
        $this->database->waitAll(); 
        $this->database->executeGeneric("practice.init.settings");
        $this->database->waitAll();
        $this->database->executeGeneric("practice.init.ban");
        $this->database->waitAll();
        $this->database->executeGeneric("practice.init.staff");
        $this->database->waitAll();
        $this->getLogger()->notice("Database connected");

        //AGGREGATES
        $this->deleteCommand(["pardon", "kick", "plugins", "version", "pardon-ip", "me", "ban", "ban-ip", "banlist"]);
        $this->addCommand([new MaintenanceCommand($this), new HubCommand($this), new BanCommand($this), new InfoCommand($this), new ArenaCommand($this), new DuelCommand($this), new KitCommand($this)]);
        $this->addDirectory(["arenas", "cosmetics", "capes", "kits", "default"]);
        $this->saveFiles(["capes/1.png", "default/default_texture.png", "default/default_geometry.json"]);

        //INITIALIZERS
        new TaskManager($this);
        PracticeListener::init();
        ItemManager::init();
        KitManager::getInstance()->init();
        ArenaManager::getInstance()->init();
        new QueueHandler();
        new DuelHandler();

        //SERVER
        $this->seasonStart = DateTime::createFromFormat("Y-m-d", $this->getConfig()->get("season-start"), new DateTimeZone("America/Mexico_City"));
        $this->seasonEnd = DateTime::createFromFormat("Y-m-d", $this->getConfig()->get("season-end"), new DateTimeZone("America/Mexico_City"));
        var_dump(DateTime::getLastErrors());
        
        $this->getServer()->getConfigGroup()->setConfigInt("max-players", Practice::SERVER_MAX_PLAYERS);
        $this->getServer()->getConfigGroup()->setConfigInt("view-distance", 16);
        $this->getServer()->getConfigGroup()->setConfigInt("difficulty", World::DIFFICULTY_PEACEFUL);
        $this->getServer()->getConfigGroup()->setConfigString("motd", Practice::SERVER_NAME);
        $this->getServer()->getConfigGroup()->save();
        
        //TASKS
        $this->addRepeatTask(new BroadcastTask(), Time::minutesToTicks(10));
        
        //EVENTS
        $this->getServer()->getPluginManager()->registerEvents(new PracticeListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new DuelListener(), $this);

        //ENABLED
        $this->getLogger()->info(TextFormat::GREEN . "has been activated successfully!!");
    }

    function onDisable(): void
    {
    	//DATABASE
        if(isset($this->database)) {
          $this->database->waitAll();
          $this->database->close();
          $this->getLogger()->warning("[Database] has been closed");
        }
        foreach(SessionManager::getInstance()->all() as $session) {
          unset($session);
        }
    }

    function getDatabase(): DataConnector
    {
        return $this->database;
    }

    function getSeasonStart(): \DateTime
    {
      return $this->seasonStart;
    }

    function getSeasonEnd(): \DateTime
    {
      return $this->seasonEnd;
    }
    
    function addCommand(array $value): void
    {
    	$this->getServer()->getCommandMap()->registerAll("practice", $value);
    }
    
    function addDirectory(array $value): void
    {
    	foreach($value as $folder) {
            @mkdir($this->getDataFolder() . $folder . DIRECTORY_SEPARATOR);
         }
    }

    function saveFiles(array $list): void
    {
      foreach($list as $value) {
        $this->saveResource($value);
      }
    }
    
    function addRepeatTask($task, int $tick): void
    {
    	$this->getScheduler()->scheduleRepeatingTask($task, $tick);
    }
    
    function deleteCommand(array $value): void
    {
      foreach($value as $cmd) {
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand($cmd));
      }
    }
    
}
