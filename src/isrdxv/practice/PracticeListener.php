<?php

namespace isrdxv\practice;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\{
  ItemManager,
  SessionManager
};

use pocketmine\Server;
use pocketmine\player\{
  Player,
  XboxLivePlayerInfo
};
use pocketmine\utils\{
  TextFormat,
  Config
};
use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerJoinEvent,
    PlayerRespawnEvent,
    PlayerPreLoginEvent,
    PlayerQuitEvent
};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

use poggit\libasynql\SqlThread;

use IvanCraft623\RankSystem\session\SessionManager as SessionRank;

class PracticeListener implements Listener
{
  
  static array $bypass = [];
  
  static function init(): void
  {
    foreach(Practice::BYPASS as $name) {
      self::$bypass[$name] = true;
    }
  }
  
  function onPreLogin(PlayerPreLoginEvent $event): void
  {
    if (Server::getInstance()->hasWhitelist()) {
      $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_SERVER_WHITELISTED, TextFormat::colorize("&8[&l&c!&r&8]" . Practice::SERVER_NAME . "&8[&l&c!]" . TextFormat::EOL . " " . TextFormat::GRAY . "Whitelist enabled!!" . TextFormat::EOL . TextFormat::RED . "The server is whitelisted." . DIRECTORY_SEPARATOR . "please look at our discord announcements"));
      return;
    }
    if (Practice::getMaintenance()) {
      $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, TextFormat::BOLD . TextFormat::RED . "Network Maintenance" . TextFormat::EOL . TextFormat::EOL . TextFormat::RESET . TextFormat::GRAY . "Server is currently in maintenance, for" . TextFormat::EOL . "more information join " . Practice::SERVER_COLOR . "discord.gg/strommc");
      return;
    }
    $info = $event->getPlayerInfo();
    if (!$info instanceof XboxLivePlayerInfo || $info->getUuid()->getBytes() === "") {
      $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, TextFormat::BOLD . TextFormat::RED . "You must login in Xbox Live before playing");
      return;
    }
    $extraData = $info->getExtraData();
    if ($extraData["DeviceOS"] === DeviceOS::ANDROID) {
      $toolbox = explode(" ", $extraData["DeviceModel"])[0];
      if ($toolbox !== strtoupper($toolbox)) {
        $reason = TextFormat::BOLD . TextFormat::RED . "Network Kick" . TextFormat::EOL . TextFormat::EOL . TextFormat::RESET;
        $reason .= TextFormat::RED . "Reason" . TextFormat::DARK_GRAY . ": " . TextFormat::GRAY . "Toolbox is not allowed" . TextFormat::EOL;
        $reason .= TextFormat::RED . "Kicked by " . TextFormat::GRAY . "StromMC Network";
        $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, $reason);
      }
    }
    $name = $info->getUsername();
    $session = SessionRank::getInstance()->get($name);
    $rank = $session->getHighestRank();
    if ($rank !== null) {
      if (in_array($rank->getName(), Practice::RANK_SUPERIORS, true)) {
        self::$bypass[$name] = true;
      }
    }
    if ($event->isKickFlagSet(PlayerPreLoginEvent::KICK_FLAG_SERVER_FULL)) {
      if (!self::ignore($name)) {
        return;
      }
      $event->clearKickFlag(PlayerPreLoginEvent::KICK_FLAG_SERVER_FULL);
    }
    $serverPro = new Config(Server::getInstance()->getDataPath() . "server.properties", Config::PROPERTIES);
    $players = $serverPro->getNested("max-players");
    $serverPro->setNested("max-players", $players);
    $serverPro->save();
  }
  
  function onJoin(PlayerJoinEvent $event): void
  {
    $player = $event->getPlayer();
    $event->setJoinMessage(TextFormat::colorize("&0[&a+&0] &a" . $player->getName()));
    $player->sendMessage(TextFormat::GRAY . "NOW Loading your data & cosmetics...");
    $information = [
      TextFormat::GRAY . "Welcome to " . Practice::SERVER_COLOR . "StromMC" . TextFormat::EOL,
      TextFormat::WHITE . "——————" . TextFormat::EOL,
      TextFormat::GRAY . "Discord: " . TextFormat::WHITE . "discord.gg/strommc" . TextFormat::EOL,
      TextFormat::DARK_RED . "Store: " . TextFormat::WHITE . "strommc.tebex.io" . TextFormat::EOL,
      TextFormat::WHITE . "——————" . TextFormat::EOL
    ];
    $player->sendMessage("\n", $information);
    $session = SessionManager::getInstance()->get($player);
    $sessionRank = SessionRank::getInstance()->get($player);
    $database = PracticeLoader::getInstance()->getDatabase();
    if (!$player->hasPlayedBefore()) {
    	var_dump($sessionRank->getHighestRank());
    	$session->init($database, $sessionRank->getHighestRank()->getName());
        return;
    }
    $xuid = $player->getXuid() !== null ? $player->getXuid() : null;
    $database->executeImplRaw([0 => "SELECT * FROM data_user,settings WHERE data_user.xuid = $xuid AND settings.xuid = $xuid"], [0 => []], [0 => SqlThread::MODE_SELECT], function(array $rows) use($session, $xuid): void {
    	if (isset($rows[0], $rows[0]->getRows()[0]) && $xuid !== null) {
    	   $session->load($rows[0]->getRows()[0]);
           return;
        } else {
          $player->kick("not xuid");
        }
    }, null);
  }
  
  function onRespawn(PlayerRespawnEvent $event): void
  {
    $player = $event->getPlayer();
    $event->setRespawnPosition(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
    ItemManager::spawnLobbyItems($player);
  }
  
  function onQuit(PlayerQuitEvent $event): void
  {
    $player = $event->getPlayer();
    $session = SessionManager::getInstance()->get($player);

    $event->setQuitMessage(TextFormat::colorize("&0[&c-&0] &c" . $player->getName()));
  }
  
  function onInteract(PlayerInteractEvent $event): void
  {
    
  }
  
  function onDamageEntity(EntityDamageByEntityEvent $event): void
  {
    $kicker = $event->getEntity();
    $damager = $event->getDamager();
    $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
    if ($kicker instanceof Player && $damager instanceof Player) {
      if ($kicker->getWorld() === $defaultWorld && $damager->getWorld() === $defaultWorld) {
        if ($damager->getInventory()->getItemInHand()->getNamedTag()->getTag("Practice")?->getValue() === ItemManager::DUEL) {
          $damager->sendMessage("lol");
        }
        $event->cancel();
        return;
      }
    }
  }
  
  function onQuery(QueryRegenerateEvent $event): void
  {
    $queryInfo = $event->getQueryInfo();

    $queryInfo->setListPlugins(false);
    $queryInfo->setServerName(TextFormat::colorize("&l&3StromMC"));
    $queryInfo->setMaxPlayerCount(($queryInfo->getPlayerCount() + 1));
  }

  static function ignore(string $name): bool
  {
    return isset(self::$bypass[strtolower($name)]);
  }
  
}