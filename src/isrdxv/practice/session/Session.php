<?php

namespace isrdxv\practice\session;

use isrdxv\practice\session\misc\{
	ClientDataInfo
};
use isrdxv\practice\PracticeLoader;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

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
    
    private string $firstPlayed;
    
    private string $lastPlayed;
    
    private ClientDataInfo $clientData;
    
    private int $kills = 0;
    
    private int $wins = 0;
    
    private int $deaths = 0;
    
    private array $settings = [];
    
    private int $address;
    
    function __construct(string $name)
    {
        $this->name = $name;
        $this->clientData = new ClientDataInfo($this->getPlayer()->getPlayerInfo()->getExtraData());
    }

    function getPlayer(): ?Player
    {
        return Server::getInstance()->getPlayerExact($this->name) ?? null;
    }
    
    function getClientData(): ClientDataInfo
    {
    	return $this->clientData;
    }
    
    function init($database, string $rank): void
    {
    	$player = $this->getPlayer();
        $xuid = $player->getXuid();
        var_dump($xuid);
        $session = $this;
        $time = new DateTime("NOW", new DateTimeZone("America/Mexico_City"));
        $address = $player instanceof FakePlayerNetworkSession ? $player->getNetworkSession()->getIp() : $player->getNetworkSession()->getIp();
    	$database->executeInsert("practice.data", ["xuid" => $xuid, "name" => $player->getName(), "custom_name" => "null", "rank" => $rank, "language" => "en_US", "coin" => 200, "firstplayed" => $time->format("Y-m-d H:i"), "lastplayed" => $time->format("Y-m-d H:i"), "kills" => 0, "wins" => 0, "deaths" => 0, "address" => $address, "device" => $this->clientData->getDevice(), "control" => $this->clientData->getTouch()]);
        $database->executeInsert("practice.settings", ["xuid" => $xuid, "scoreboard" => true, "queue" => true, "cps" => false, "auto_join" => false]);
        $database->executeImplRaw([0 => "SELECT * FROM data_user,settings WHERE data_user.xuid = $xuid AND settings.xuid = $xuid"], [0 => []], [0 => SqlThread::MODE_SELECT], function(array $rows) use($session, $player, $xuid): void {
        	if ($player instanceof Player) {
        	  var_dump($rows[0]->getRows());
        	   if (isset($rows[0], $rows[0]->getRows()[0]) && $xuid !== null) {
        	      $session->load($rows[0]->getRows()[0]);
        	   }
        	}
        }, null);
    }
    
    function load(array $data): void
    {
    	$this->settings = ["scoreboard" => $data["scoreboard"], "queue" => $data["queue"], "cps" => $data["cps"], "auto_join" => $data["auto_join"]];
    	$this->address = intval($data["address"]);
    	$this->deaths = $data["deaths"];
    	$this->wins = $data["wins"];
    	$this->kills = $data["kills"];
    	$this->firstPlayed = $data["firstplayed"];
    	$this->lastPlayed = $data["lastplayed"];
    	$this->coin = $data["coin"];
    	$this->language = $data["language"];
    	$this->rank = $data["rank"];
    	$this->customName = ($data["custom_name"] === null || $data["custom_name"] === "null") ? null : $data["custom_name"];
    	$this->xuid = $data["xuid"];
        $player = $this->getPlayer();
        $device = $this->clientData->getDevice();
        $control = $this->clientData->getTouch();
        $player->setScoreTag(TextFormat::colorize("&f". $device . " &b| &f" . $control));
    }

    function save(): void
    {
    	$database = PracticeLoader::getInstance()->getDatabase();
    }
}