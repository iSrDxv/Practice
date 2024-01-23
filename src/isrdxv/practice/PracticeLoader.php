<?php

namespace isrdxv\practice;

use isrdxv\practice\{
  Practice,
  PracticeListener
};
use isrdxv\practice\command\{
	MaintenanceCommand
};
use isrdxv\practice\task\BroadcastTask;
use isrdxv\practice\utils\Time;

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
        $this->database->waitAll(); $this->database->executeGeneric("practice.init.settings", [], function(): void {}, null);
        $this->database->waitAll();
        $this->getLogger()->notice("Database connected");

        //AGGREGATES
        $this->addCommand([new MaintenanceCommand($this, "maintenance", TextFormat::DARK_AQUA . "Enable or disable the server under maintenance")]);
        $this->addDirectory(["arenas", "cosmetics", "capes"]);
        
        //INITIALIZERS
        PracticeListener::init();
        
        //SERVER
        $this->getServer()->getConfigGroup()->setConfigInt("max-players", Practice::SERVER_MAX_PLAYERS);
        $this->getServer()->getConfigGroup()->setConfigInt("view-distance", 16);
        $this->getServer()->getConfigGroup()->setConfigInt("difficulty", World::DIFFICULTY_PEACEFUL);
        $this->getServer()->getConfigGroup()->setConfigString("server-name", Practice::SERVER_NAME);
        $this->getServer()->getConfigGroup()->save();
        
        //TASKS
        $this->addRepeatTask(new BroadcastTask(), Time::minutesToTicks(10));
        
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
}