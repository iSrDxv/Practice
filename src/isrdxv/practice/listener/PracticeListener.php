<?php

namespace isrdxv\practice\listener;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\{
    ArenaManager,
    KitManager,
    ItemManager,
    SessionManager
};
use isrdxv\practice\form\{
  duel\DuelMenuForm,
  duel\DuelRequestForm,
  user\profile\ProfileMenuForm
};
use isrdxv\practice\form\ffa\FFAForm;
use isrdxv\practice\handler\QueueHandler;

use pocketmine\Server;
use pocketmine\player\{
  Player,
  GameMode,
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
    PlayerQuitEvent,
    PlayerItemUseEvent,
    PlayerDropItemEvent
};
use pocketmine\event\block\{
  BlockPlaceEvent,
  BlockBreakEvent
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
      $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_SERVER_WHITELISTED, TextFormat::DARK_GRAY . "[" . TextFormat::BOLD . TextFormat::RED . "!" . TextFormat::RESET . TextFormat::DARK_GRAY . "]" . Practice::SERVER_NAME . TextFormat::DARK_GRAY . "[" . TextFormat::BOLD . TextFormat::RED . "!" . TextFormat::RESET . TextFormat::DARK_GRAY . "]" . TextFormat::EOL . " " . TextFormat::GRAY . "Whitelist enabled!!" . TextFormat::EOL . TextFormat::RED . "The server is whitelisted." . TextFormat::EOL . "please look at our discord announcements");
      return;
    }
    if (Practice::getMaintenance()) {
      $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, TextFormat::BOLD . TextFormat::RED . "Network Maintenance" . TextFormat::EOL . TextFormat::EOL . TextFormat::RESET . TextFormat::GRAY . "Server is currently in maintenance, for" . TextFormat::EOL . "more information join " . Practice::SERVER_COLOR . "discord.gg/strommc");
      return;
    }
    $newTime = new \DateTime(timezone: new \DateTimeZone("America/Mexico_City"));
    if (PracticeLoader::getInstance()->getSeasonEnd()->format("Y-m-d H:i") === $newTime->format("Y-m-d H:i")) {
      $event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, Practice::SERVER_NAME . TextFormat::WHITE . " Network " . TextFormat::GRAY . "-" .  TextFormat::DARK_AQUA . " SEASON" . TextFormat::EOL . TextFormat::EOL . TextFormat::RESET . TextFormat::YELLOW . "The season is over, thanks for playing our server!!");
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
    $player->sendMessage(TextFormat::GRAY . "NOW Loading your data & cosmetics...");
    $seasonInfo = [
      Practice::SERVER_COLOR . TextFormat::BOLD . "Practice " . TextFormat::RESET . "- " . TextFormat::WHITE . "Season " . Practice::SERVER_COLOR . Practice::SEASON,
      TextFormat::DARK_GRAY . " " . TextFormat::EOL,
      TextFormat::GRAY . TextFormat::BOLD . "» Season started on " . TextFormat::AQUA . date("F/m Y", PracticeLoader::getInstance()->getSeasonStart()->getTimestamp()),
      TextFormat::GRAY . TextFormat::BOLD . "» Season ends on " . TextFormat::RED . date("F/m Y", PracticeLoader::getInstance()->getSeasonEnd()->getTimestamp()),
      TextFormat::WHITE . " " . TextFormat::EOL
    ];
    $player->sendMessage(implode("\n", $seasonInfo));
    $information = [
      TextFormat::GRAY . "Welcome " . TextFormat::GRAY . $player->getName() . " to " . Practice::SERVER_COLOR . "StromMC!" . TextFormat::EOL,
      TextFormat::WHITE . " " . TextFormat::EOL,
      TextFormat::DARK_AQUA . "Discord: " . TextFormat::WHITE . Practice::DISCORD_LINK . TextFormat::EOL,
      TextFormat::GREEN . "Store: " . TextFormat::WHITE . "strommc.tebex.io" . TextFormat::EOL,
      TextFormat::WHITE . " " . TextFormat::EOL
    ];
    $player->sendMessage(implode("\n", $information));
    if (!SessionManager::getInstance()->set($player)) {
      $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GOLD . "Loading your session...");
      sleep(2500);
      $player->kick(TextFormat::RED . "The session was not created correctly, re-enter the server.");
      return;
    }
    $session = SessionManager::getInstance()->get($player);
    $sessionRank = SessionRank::getInstance()->get($player);
    $database = PracticeLoader::getInstance()->getDatabase();
    var_dump($sessionRank->getHighestRank());
    if (!$player->hasPlayedBefore()) {
    	$session->init($database, $sessionRank->getHighestRank()->getName());
        return;
    }
    $xuid = $player->getXuid() !== null ? $player->getXuid() : null;
    $database->executeImplRaw([0 => "SELECT * FROM data_user,settings WHERE data_user.xuid = $xuid AND settings.xuid = $xuid"], [0 => []], [0 => SqlThread::MODE_SELECT], function(array $rows) use($player, $session, $xuid): void {
    	if (isset($rows[0], $rows[0]->getRows()[0]) && $xuid !== null) {
    	   $session->load($rows[0]->getRows()[0]);
           return;
        } else {
          $player->kick("not xuid");
        }
    }, null);
    switch($sessionRank->getHighestRank()->getName()) {
      case "User":
        $event->setJoinMessage(TextFormat::BLACK . "[" . TextFormat::GREEN . "+" . TextFormat::BLACK . "] " . TextFormat::GREEN . $player->getName());
      break;
      case "Admin":
        $event->setJoinMessage(TextFormat::DARK_RED . "[" . TextFormat::WHITE . "+" . TextFormat::DARK_RED . "] " . TextFormat::WHITE . $player->getName());
      break;
      case "Owner":
        $event->setJoinMessage(TextFormat::DARK_RED . "[" . TextFormat::BLUE . "+" . TextFormat::DARK_RED . "] " . TextFormat::BLUE . $player->getName());
      break;
      case "YouTube":
        $event->setJoinMessage(TextFormat::RED . "[" . TextFormat::GOLD . "+" . TextFormat::WHITE . "] " . TextFormat::WHITE . $player->getName());
      break;
      case "Godex":
        $event->setJoinMessage(TextFormat::GOLD . "[" . TextFormat::WHITE . "+" . TextFormat::GOLD . "] " . TextFormat::GOLD . $player->getName());
      break;
      case "Zodiac":
        $event->setJoinMessage(TextFormat::DARK_BLUE . "[" . TextFormat::WHITE . "+" . TextFormat::DARK_BLUE . "] " . TextFormat::BLUE . $player->getName());
      break;
      case "Developer":
        $event->setJoinMessage(TextFormat::BLUE . "[" . TextFormat::GOLD . "+" . TextFormat::BLUE . "] " . TextFormat::AQUA . $player->getName());
      break;
      case "Mod":
        $event->setJoinMessage(TextFormat::DARK_PURPLE . "[" . TextFormat::GRAY . "+" . TextFormat::LIGHT_PURPLE . "] " . TextFormat::LIGHT_PURPLE . $player->getName());
      break;
    }
  }
  
  function onRespawn(PlayerRespawnEvent $event): void
  {
    $player = $event->getPlayer();
    $event->setRespawnPosition(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
    $player->setGamemode(GameMode::ADVENTURE());
    ItemManager::spawnLobbyItems($player);
  }
  
  function onQuit(PlayerQuitEvent $event): void
  {
    $player = $event->getPlayer();
    $session = SessionManager::getInstance()->get($player);
    $session->getScoreboardHandler()->setScoreboard(null);
    $session->save();
    
    SessionManager::getInstance()->delete($player);
    
    $event->setQuitMessage(TextFormat::colorize("&0[&c-&0] &c" . $player->getName()));
  }
  
  function onItemUse(PlayerItemUseEvent $event): void
  {
    if (($session = SessionManager::getInstance()->get(($player = $event->getPlayer()))) !== null) {
      $item = $event->getItem();
      if (($tag = $item->getNamedTag()->getTag("Practice")) !== null) {
        switch($tag?->getValue()){
          case ItemManager::DUEL:
            if ($session->isInLobby()) {
              $player->sendForm(new DuelMenuForm(["name" => $player->getName()]));
            }
          break;
          case ItemManager::FFA:
            $ffa = array_keys(ArenaManager::getInstance()->getAllNoDuel());
            if ($session->isInLobby()) {
              $player->sendForm(new FFAForm($ffa));
            }
          break;
          case ItemManager::PROFILE:
            if ($session->isInLobby()) {
              $player->sendForm(new ProfileMenuForm());
            }
          break;
          //Queue
          case ItemManager::LEAVE_QUEUE:
            if ($session->isInLobby()) {
              if ($session->getQueue() !== null) {
                $player->setGamemode(GameMode::ADVENTURE());
                ItemManager::spawnLobbyItems($player);
                $session->setQueue();
                QueueHandler::getInstance()->remove($player->getName(), true);
              }
            }
          break;
        }
      }
    }
    $event->cancel();
  }
  
  function onBreak(BlockBreakEvent $event): void
  {
    if (($session = SessionManager::getInstance()->get(($player =$event->getPlayer()))) !== null) {
      if ($session->isInLobby() && !Server::getInstance()->isOp($player->getName()) || $player->hasPermission("practice.break.blocks")) {
        $event->cancel();
      }
    }
  }
  
  function onPlace(BlockPlaceEvent $event): void
  {
    if (($session = SessionManager::getInstance()->get(($player = $event->getPlayer()))) !== null) {
      if ($session->isInLobby() && !Server::getInstance()->isOp($player->getName()) || $player->hasPermission("practice.place.blocks")) {
        $event->cancel();
      }
    }
  }
  
  function onDrop(PlayerDropItemEvent $event): void
  {
    if (($session = SessionManager::getInstance()->get(($event->getPlayer()))) !== null) {
      if ($session->isInLobby() && $event->getItem()->getNamedTag()->getTag("Practice") !== null) {
        $event->cancel();
      }
    }
  }
  
  function onDamageEntity(EntityDamageByEntityEvent $event): void
  {
    $kicker = $event->getEntity();
    $damager = $event->getDamager();
    $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
    if ($kicker instanceof Player && $damager instanceof Player) {
      if ($kicker->getWorld() === $defaultWorld && $damager->getWorld() === $defaultWorld) {
        $event->cancel();
        if ($event->getCause() === EntityDamageByEntityEvent::CAUSE_VOID) {
          $kicker->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
          return;
        }
        if ($event->getCause() === EntityDamageByEntityEvent::CAUSE_SUFFOCATION) {
          $kicker->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
          return;
        }
        if ($event->getCause() === EntityDamageByEntityEvent::CAUSE_SUICIDE) {
          $kicker->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
          return;
        }
        if ($damager->getInventory()->getItemInHand()->getNamedTag()->getTag("Practice")?->getValue() === ItemManager::DUEL) {
          $kits = array_keys(KitManager::getInstance()->getAll());
          $players = array_keys(SessionManager::getInstance()->all());
          $damager->sendForm(new DuelRequestForm($kicker, $kits, $players));
        }
      }
    }
  }
  
  function onQuery(QueryRegenerateEvent $event): void
  {
    $queryInfo = $event->getQueryInfo();

    $queryInfo->setListPlugins(false);
    $queryInfo->setServerName(TextFormat::BOLD . Practice::SERVER_NAME);
    $queryInfo->setMaxPlayerCount(($queryInfo->getPlayerCount() + 1));
  }

  static function ignore(string $name): bool
  {
    return isset(self::$bypass[strtolower($name)]);
  }
  
}