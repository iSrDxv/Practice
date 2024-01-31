<?php

namespace isrdxv\practice\session;

use isrdxv\practice\session\misc\{
	ClientDataInfo,
	ScoreboardHandler
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

class Session
{
	  private int $xuid;
	
    private string $name;
    
    private ?string $customName;
    
    private string $rank;
    
    private string $language;
    
    private int $coin;
    
    private int $elo;
    
    private string $firstPlayed;
    
    private string $lastPlayed;
    
    private ClientDataInfo $clientData;
    
    private ScoreboardHandler $scoreboardHandler;
    
    private string $oldDevice;
    
    private string $oldTouch;
    
    private int $kills = 0;
    
    private int $wins = 0;
    
    private int $deaths = 0;
    
    private array $settings = [];
    
    private int $address;
    
    function __construct(string $name)
    {
        $this->name = $name;
    }

    function getPlayer(): ?Player
    {
        return Server::getInstance()->getPlayerExact($this->name) ?? null;
    }
    
    function isInLobby(): bool
    {
        return $this->getPlayer()?->getWorld() === Server::getInstance()->getWorldManager()->getDefaultWorld();
    }
    
    function init($database, string $rank): void
    {
    	$player = $this->getPlayer();
      $xuid = $player->getXuid();
      var_dump($xuid);
      $session = $this;
      $time = new DateTime("NOW", new DateTimeZone("America/Mexico_City"));
      $address = $player instanceof FakePlayerNetworkSession ? $player->getNetworkSession()->getIp() : $player->getNetworkSession()->getIp();
      $this->clientData = new ClientDataInfo($this->getPlayer()?->getPlayerInfo()->getExtraData());
    	$database->executeInsert("practice.data", ["xuid" => $xuid, "name" => $player->getName(), "custom_name" => "null", "rank" => $rank, "language" => "en_US", "coin" => 200, "elo" => 1000, "firstplayed" => $time->format("Y-m-d H:i"), "lastplayed" => $time->format("Y-m-d H:i"), "kills" => 0, "wins" => 0, "deaths" => 0, "address" => $address, "device" => $this->clientData->getDevice(), "control" => $this->clientData->getTouch()]);
        $database->executeInsert("practice.settings", ["xuid" => $xuid, "scoreboard" => true, "queue" => true, "cps" => false, "auto_join" => false]);
        $database->executeImplRaw([0 => "SELECT * FROM data_user,settings WHERE data_user.xuid = $xuid AND settings.xuid = $xuid"], [0 => []], [0 => SqlThread::MODE_SELECT], function(array $rows) use($session, $player, $xuid): void {
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
      $this->scoreboardHandler->setScoreboard(ScoreboardHandler::TYPE_LOBBY);
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
    
    function getScoreboardHandler(): ScoreboardHandler
    {
      return $this->scoreboardHandler;
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
    	$database->executeImplRaw([0 => "UPDATE data_user SET name='$name', custom_name='$customName', rank='$rank', language='$language', coin='$coin', firstplayed='$firstPlayed', lastplayed='$lastPlayed', kills='$this->kills', wins='$this->wins', deaths='$this->deaths', address='$address', device='$device', control='$control' WHERE xuid = '$xuid'"], [0 => []], [0 => SqlThread::MODE_CHANGE], function(array $rows): void {}, null);
    	var_dump($this->settings);
    	$scoreboard = (int)$this->getSetting("scoreboard");
    	$queue = (int)$this->getSetting("queue");
    	$cps = (int)$this->getSetting("cps");
    	$autoJoin = (int)$this->getSetting("auto_join");
    	$database->executeImplRaw([0 => "UPDATE settings SET scoreboard='$scoreboard', queue='$queue', cps='$cps', auto_join='$autoJoin' WHERE xuid = $xuid"], [0 => []], [0 => SqlThread::MODE_CHANGE], function(array $rows): void {}, null);
    }
}