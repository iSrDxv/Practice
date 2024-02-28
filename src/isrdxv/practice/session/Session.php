<?php

namespace isrdxv\practice\session;

use isrdxv\practice\session\misc\{
	ClientDataInfo,
	ScoreboardHandler,
  StaffData
};
use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\ItemManager;

use pocketmine\Server;
use pocketmine\player\{
  GameMode,
  Player
};
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\AnvilFallSound;

use poggit\libasynql\SqlThread;

use muqsit\fakeplayer\network\FakePlayerNetworkSession;

use DateTime;
use DateTimeZone;

use isrdxv\practice\duel\queue\UserQueued;
use isrdxv\practice\duel\UserDuel;
use isrdxv\practice\kit\DefaultKit;
use isrdxv\practice\manager\KitManager;

use exodus\cache\CacheManager;

class Session
{
	  private string $xuid;
	
    private string $name;
    
    private ?string $customName;
    
    private string $rank;
    
    private string $language;
    
    private int $coin;
    
    private int $elo;
    
    private string $firstPlayed;
    
    private string $lastPlayed;
    
    private ClientDataInfo $clientData;
    
    private ?ScoreboardHandler $scoreboardHandler;
    
    private ?StaffData $staffData;
    
    private string $oldDevice;
    
    private string $oldTouch;
    
    private int $kills = 0;
    
    private int $wins = 0;
    
    private int $deaths = 0;
    
    private array $settings = [];
    
    private int $address;
    
    //System of Duel
    private ?UserQueued $queue = null;

    private ?UserDuel $duel = null;

    private ?DefaultKit $kit;
    
    private CacheManager $cache;
    
    function __construct(string $name)
    {
        $this->name = $name;
        $this->cache = new CacheManager();
    }

    function getPlayer(): ?Player
    {
        return Server::getInstance()->getPlayerExact($this->name) ?? null;
    }
    
    function getPing(): int
    {
      return $this->getPlayer()?->getNetworkSession()->getPing();
    }

    function isInLobby(): bool
    {
      return $this->getPlayer()?->getWorld() === Server::getInstance()->getWorldManager()->getDefaultWorld();
    }
    
    function clear(): void
    {
      $player = $this->getPlayer();
      $player?->getInventory()->clearAll();
      $player?->getArmorInventory()->clearAll();
      $player?->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
      $player?->getXpManager()->setXpAndProgress(0, 0.0);
      $player?->setGamemode(GameMode::ADVENTURE());
      $player?->setHealth($player->getMaxHealth());
      $player?->setFlying(false);
      $player?->setAllowFlight(false);
      $player?->getEffects()->clear();
    }

    function setDuel(?UserDuel $duel = null): void
    {
      $this->duel = $duel;
    }

    function getDuel(): ?UserDuel
    {
      return $this->duel ?? null;
    }

    function setQueue(?UserQueued $queue = null): void
    {
      $this->queue = $queue;
    }

    function getQueue(): ?UserQueued
    {
      return $this->queue ?? null;
    }

    function setKit(string|DefaultKit $kit): void
    {
      if (is_string($kit)) {
        $kit = KitManager::getInstance()->get($kit);
      }
      $this->kit = $kit;
    }

    function getKit(): ?DefaultKit
    {
      return $this->kit ?? null;
    }
    
    function getCache(): CacheManager
    {
      return $this->cache;
    }
    
    function init($database, string $rank): void
    {
    	$player = $this->getPlayer();
      $xuid = $player->getXuid();
      var_dump($xuid);
      $session = $this;
      $time = new DateTime("NOW", new DateTimeZone("America/Mexico_City"));
      $address = $player->getNetworkSession()->getIp() ?? "127.0.0.1";
      $this->clientData = new ClientDataInfo($this->getPlayer()?->getPlayerInfo()->getExtraData());
      if (in_array($rank, Practice::ADMINISTRATIVE_RANKS, true)) {
        $database->executeInsert("practice.insert.staff.stats", ["xuid" => $xuid, "name" => (string)$player->getName(), "bans" => 0, "kicks" => 0, "mutes" => 0, "reports" => 0]);
      }
    	$database->executeInsert("practice.insert.data", ["xuid" => $xuid, "name" => $player->getName(), "custom_name" => "null", "rank" => $rank, "language" => "en_US", "coin" => 200, "elo" => 1000, "firstplayed" => $time->format("Y-m-d H:i"), "lastplayed" => $time->format("Y-m-d H:i"), "kills" => 0, "wins" => 0, "deaths" => 0, "address" => $address, "device" => $this->clientData->getDevice(), "control" => $this->clientData->getTouch()]);
        $database->executeInsert("practice.insert.settings", ["xuid" => $xuid, "scoreboard" => true, "queue" => true, "cps" => false, "auto_join" => false]);
        $database->executeImplRaw([0 => "SELECT * FROM user,settings WHERE user.xuid = $xuid AND settings.xuid = $xuid"], [0 => []], [0 => SqlThread::MODE_SELECT], function(array $rows) use($session, $player, $xuid): void {
        	if ($player instanceof Player) {
        	  var_dump("init");
        	  var_dump($rows[0]->getRows());
        	   if (isset($rows[0], $rows[0]->getRows()[0]) && $xuid !== null) {
        	      $session->load($rows[0]->getRows()[0]);
        	   }
        	}
        }, null);
    }
    
    function load(array $data): void
    {
    	$this->settings = ["scoreboard" => boolval($data["scoreboard"]), "queue" => boolval($data["queue"]), "cps" => boolval($data["cps"]), "auto_join" => boolval($data["auto_join"])];
    	$this->address = intval($data["address"]);
    	$this->deaths = $data["deaths"];
    	$this->wins = $data["wins"];
    	$this->kills = $data["kills"];
    	$this->firstPlayed = $data["firstplayed"];
    	$this->lastPlayed = $data["lastplayed"];
    	$this->coin = $data["coin"];
    	$this->elo = $data["elo"];
    	$this->language = $data["language"];
    	$this->rank = $data["rank"];
    	$this->customName = ($data["custom_name"] === null || $data["custom_name"] === "null") ? null : $data["custom_name"] ?? "";
    	$this->xuid = $data["xuid"];
      $player = $this->getPlayer();
      $staffData = null;
      if (in_array($this->rank, Practice::ADMINISTRATIVE_RANKS, true)) {
        (PracticeLoader::getInstance()->getDatabase())->executeImplRaw([0 => "SELECT * FROM staff_stats WHERE xuid=$this->xuid"], [0 => []], [0 => SqlThread::MODE_SELECT], function(array $rows) use($player, $staffData): void {
          if (isset($rows[0], $rows[0]->getRows()[0])) {
            $staffData = new StaffData($rows[0]->getRows()[0]);
          }
        }, null);
      }
      $this->staffData = $staffData;
      $this->clientData = new ClientDataInfo($this->getPlayer()?->getPlayerInfo()->getExtraData());
      $device = $this->clientData->getDevice();
      $control = $this->clientData->getTouch();
      $this->oldDevice = (string)$data["device"];
      $this->oldTouch = (string)$data["control"];
      $player->sendMessage(implode("\n", [Practice::SERVER_PREFIX . TextFormat::RED . "Last time you entered with: " . TextFormat::WHITE . $this->oldDevice, Practice::SERVER_PREFIX . TextFormat::RED . "And the last time you were: " . TextFormat::WHITE . $this->oldTouch, Practice::SERVER_PREFIX . TextFormat::GRAY . "Hopefully it's you, but on another device"]));
      $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
      $defaultWorld->setTime(0);
      $defaultWorld->stopTime();
      $defaultWorld->loadChunk($defaultWorld->getSpawnLocation()->getX(), $defaultWorld->getSpawnLocation()->getZ());
      $player->teleport($defaultWorld->getSpawnLocation());
      $this->scoreboardHandler = new ScoreboardHandler($player);
      $this->scoreboardHandler?->setScoreboard(ScoreboardHandler::TYPE_LOBBY);
      $player->setGamemode(GameMode::ADVENTURE());
      $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "Your account details uploaded correctly!!");
      $player->setScoreTag(TextFormat::WHITE . $device . TextFormat::DARK_GRAY . " | " . Practice::SERVER_COLOR . $control);
      $player->broadcastSound(new AnvilFallSound(), [$player]);
      ItemManager::spawnLobbyItems($player);
    }

    function getCustomName(): ?string
    {
      return $this->customName;
    }
    
    function getRank(): string
    {
      return $this->rank;
    }
    
    function getLanguage(): string
    {
      return $this->language;
    }
    
    function getCoin(): int
    {
      return $this->coin;
    }
    
    function getElo(): int
    {
      return $this->elo;
    }
    
    function getFirstPlayed(): string
    {
      return $this->firstPlayed;
    }
    
    function getLastPlayed(): string
    {
      return $this->lastPlayed;
    }
    
    function getClientData(): ClientDataInfo
    {
    	return $this->clientData;
    }
    
    function getScoreboardHandler(): ?ScoreboardHandler
    {
      return $this->scoreboardHandler ?? null;
    }
    
    function getStaffData(): ?StaffData
    {
      return $this->staffData ?? null;
    }
    
    function getKills(): int
    {
      return $this->kills;
    }
    
    function getWins(): int
    {
      return $this->wins;
    }
    
    function getDeaths(): int
    {
      return $this->deaths;
    }
    
    function setSetting(string $adjustment, bool $value): void
    {
      $this->settings[$adjustment] = $value;
    }
    
    function getSetting(string $value): bool
    {
      return $this->settings[$value];
    }
    
    function getSettings(): array
    {
      return $this->settings;
    }
    
    function save(): void
    {
      $xuid = $this->getPlayer()?->getXuid();
      $lastPlayed = (new DateTime("NOW", new DateTimeZone("America/Mexico_City")))->format("Y-m-d H:i");
      $address = (string)$this->getPlayer()?->getNetworkSession()->getIp() ?? "127.0.0.1";
      $device = $this->clientData->getDevice();
      $control = $this->clientData->getTouch();
      $name = $this->name;
      $customName = $this->customName ?? "null";
      $rank = $this->rank;
      $language = $this->language;
      $coin = $this->coin;
      $elo = $this->elo;
      $firstPlayed = $this->firstPlayed;
    	$database = PracticeLoader::getInstance()->getDatabase();
    	$database->executeImplRaw([0 => "UPDATE user SET name='$name', custom_name='$customName', rank='$rank', language='$language', coin='$coin', elo='$elo', firstplayed='$firstPlayed', lastplayed='$lastPlayed', kills='$this->kills', wins='$this->wins', deaths='$this->deaths', address='$address', device='$device', control='$control' WHERE xuid = '$xuid'"], [0 => []], [0 => SqlThread::MODE_CHANGE], function(array $rows): void {}, null);
    	var_dump($this->settings);
    	$scoreboard = (int)$this->getSetting("scoreboard");
    	$queue = (int)$this->getSetting("queue");
    	$cps = (int)$this->getSetting("cps");
    	$autoJoin = (int)$this->getSetting("auto_join");
    	$database->executeImplRaw([0 => "UPDATE settings SET scoreboard='$scoreboard', queue='$queue', cps='$cps', auto_join='$autoJoin' WHERE xuid = $xuid"], [0 => []], [0 => SqlThread::MODE_CHANGE], function(array $rows): void {}, null);
    	if (($staffData = $this->staffData) !== null) {
    	  $staffData->save($database, $xuid, $name);
    	}
    }
    
}
