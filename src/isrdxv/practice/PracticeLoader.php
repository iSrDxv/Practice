<?php

namespace isrdxv\practice;

use isrdxv\practice\{
    Practice,
    PracticeListener
};
use isrdxv\practice\command\{
    HubCommand,
    BanCommand,
    InfoCommand,
    ArenaCommand,
    DuelCommand,
    KitManager,
  	MaintenanceCommand
};
use isrdxv\practice\task\BroadcastTask;
use isrdxv\practice\utils\Time;
use isrdxv\practice\manager\{
    TaskManager,
    ItemManager
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

class PracticeLoader extends PluginBase
{
    use SingletonTrait;

    private $database;
    
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
        $this->database->executeGeneric("practice.init.user", [], function(): void {}, null);
        $this->database->waitAll(); 
        $this->database->executeGeneric("practice.init.settings", [], function(): void {}, null);
        $this->database->waitAll();
        $this->database->executeGeneric("practice.init.ban", [], function(): void {}, null);
        $this->database->waitAll();
        $this->database->executeGeneric("practice.init.staff", [], function(): void {}, null);
        $this->database->waitAll();
        $this->getLogger()->notice("Database connected");

        //AGGREGATES
        $this->deleteCommand(["pardon", "kick", "plugins", "version", "pardon-ip", "me", "ban", "ban-ip", "banlist"]);
        $this->addCommand([new MaintenanceCommand($this), new HubCommand($this), new BanCommand($this), new InfoCommand($this), new ArenaCommand($this), new DuelCommand($this), new KitManager($this)]);
        $this->addDirectory(["arenas", "cosmetics", "capes", "kits"]);
        
        //INITIALIZERS
        new TaskManager($this);
        PracticeListener::init();
        ItemManager::init();
        
        //SERVER
        $this->getServer()->getConfigGroup()->setConfigInt("max-players", Practice::SERVER_MAX_PLAYERS);
        $this->getServer()->getConfigGroup()->setConfigInt("view-distance", 16);
        $this->getServer()->getConfigGroup()->setConfigInt("difficulty", World::DIFFICULTY_PEACEFUL);
        $this->getServer()->getConfigGroup()->setConfigString("motd", Practice::SERVER_NAME);
        $this->getServer()->getConfigGroup()->save();
        
        //TASKS
        $this->addRepeatTask(new BroadcastTask(), Time::minutesToTicks(8));
        
        //EVENTS
        $this->getServer()->getPluginManager()->registerEvents(new PracticeListener(), $this);
        
        //ENABLED
        $this->getLogger()->info(TextFormat::GREEN . "has been activated successfully!!");
    }

    function onDisable(): void
    {
    	//DATABASE
        if(isset($this->database)) {
          $this->database->close();
          $this->getLogger()->warning("[Database] has been closed");
        }
    }

    function getDatabase(): DataConnector
    {
        return $this->database;
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