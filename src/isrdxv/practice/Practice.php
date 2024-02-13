<?php

namespace isrdxv\practice;

use isrdxv\practice\PracticeLoader;
use isrdxv\practice\kit\misc\KnockbackInfo;

use pocketmine\item\{
  Item,
  enchantment\EnchantmentInstance
};
use pocketmine\entity\effect\EffectInstance;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\entity\{
  Attribute,
  Living
};
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\utils\TextFormat;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\data\bedrock\{
  EffectIdMap,
  EnchantmentIdMap
};
use pocketmine\data\SavedDataLoadingException;

/**
 * NOTE: Strageehh se la come :v
 */
class Practice
{
    static bool $maintenance = false;
    
    const ADMINISTRATIVE_RANKS = ["Owner", "Admin", "Mod", "Developer", "Helper"];
    
    const RANK_SUPERIORS = ["Owner", "Admin", "Mod", "Developer", "Strom", "Zodiac", "YouTuber", "Streamer"];
    
    const BYPASS = ["SrClauYT", "StyleMH"];
    
    const SERVER_NAME = TextFormat::LIGHT_PURPLE . "StromMC";
    
    const SERVER_COLOR = TextFormat::LIGHT_PURPLE;
    
    const SERVER_MOTD = self::SERVER_NAME . ": " . TextFormat::BOLD . TextFormat::WHITE . "¡¡NEW RELEASE!!" . TextFormat::GRAY;

    const SERVER_MAX_PLAYERS = 1; //100

    const SERVER_PREFIX = self::SERVER_NAME . TextFormat::BOLD . TextFormat::GRAY . " » " . TextFormat::RESET;
    
    const BROADCAST_LIST = [
         TextFormat::RESET . TextFormat::GRAY . "Don't forget to enter our store to see the benefits: " . self::SERVER_COLOR . "strommc.tebex.io",
         TextFormat::RESET . TextFormat::GREEN . "Don't forget to enjoy our server, and vote to get a rank for 1 month: link"
    ];
    
    const NO_QUEUE_BE = [
      "macOS",
      "FireOS",
      "Windows 10",
      "Windows 7",
      "FireOS"
    ];

    const charWidths = [
    		" " => 4,
    		"!" => 2,
    		"'" => 5,
    		"\'" => 3,
    		"(" => 5,
    		")" => 5,
    		"*" => 5,
    		"," => 2,
    		"." => 2,
    		":" => 2,
    		";" => 2,
    		"<" => 5,
    		">" => 5,
    		"@" => 7,
    		"I" => 4,
    		"[" => 4,
    		"]" => 4,
    		"f" => 5,
    		"i" => 2,
    		"k" => 5,
    		"l" => 3,
    		"t" => 4,
    		"" => 5,
    		"|" => 2,
    		"~" => 7,
    		"█" => 9,
    		"░" => 8,
    		"▒" => 9,
    		"▓" => 9,
    		"▌" => 5,
    		"─" => 9
    ];

    static function setMaintenance(): void
    {
      self::$maintenance = PracticeLoader::getInstance()->getConfig()->getNested("maintenance");
    }
    
    static function getMaintenance(): bool
    {
      return self::$maintenance;
    }
    
    static function getRandomId(): string
    {
      return bin2hex(random_bytes(8));
    }
    
    static function knockBack(Player $player, Living $entity, KnockbackInfo $kbInfo): void
    {
      $horizontal = $kbInfo->horizontal;
      $vertical = $kbInfo->vertical;
      if (!$player->isOnGround() &&  ($maxHeight = $kbInfo->maxHeight) > 0) {
        [$max, $min] = self::maxAndMin($player->getPosition()->y, $entity->getPosition()->y);
        if ($max - $min >= $maxHeight) {
          $vertical *= 0.75;
          if ($kbInfo->canRevert) {
            $vertical *= -1;
          }
        }
      }
      $x = $player->getPosition()->x - $entity->getPosition()->x;
      $y = $player->getPosition()->y - $entity->getPosition()->y;
      $z = $player->getPosition()->z - $entity->getPosition()->z;
      $f = sqrt($x * $x + $z * $z);
      if ($f <= 0) {
        return;
      }
      if (mt_rand() / mt_getrandmax() > $player->getAttributeMap()->get(Attribute::KNOCKBACK_RESISTANCE)?->getValue()) {
        $f = 1 / $f;
        $motion = clone $player->getMotion();
        $motion->x /= 2;
			  $motion->y /= 2;
			  $motion->z /= 2;
  			$motion->x += $x * $f * $horizontal;
  			$motion->y += $vertical;
  			$motion->z += $z * $f * $horizontal;
  			
  			if($motion->y > $vertical){
  			  $motion->y = $vertical;
  			}
  			$player->setMotion($motion);
      }
    }
    
    static function centerText(string $input, int $maxLength = 0, bool $addRightPadding = false): string
    {
      $lines = explode("\n", trim($input));
		  $sortedLines = $lines;
		  usort($sortedLines, static function(string $a, string $b){
		    return self::getPixelLength($b) <=> self::getPixelLength($a);
		  });
		  $longest = $sortedLines[0];
  		if($maxLength === 0){
  		  $maxLength = self::getPixelLength($longest);
	   	}
		  $result = "";
  		$spaceWidth = self::getCharWidth(" ");
  		foreach($lines as $sortedLine){
  		  $len = max($maxLength - self::getPixelLength($sortedLine), 0);
			  $padding = (int) round($len / (2 * $spaceWidth));
		  	$paddingRight = (int) floor($len / (2 * $spaceWidth));
	  		$result .= str_pad(" ", $padding) . $sortedLine . ($addRightPadding ? str_pad(" ", $paddingRight) : "") . "\n";
  		}
	  	return rtrim($result, "\n");
    }
    
    static function getPixelLength(string $pixel): int
    {
      $length = 0;
      foreach(str_split(TextFormat::clean($pixel)) as $char) {
        $length += self::getCharWidth($char);
      }
      $length += substr_count($pixel, TextFormat::BOLD);
      return $length;
    }
    
    private static function getCharWidth(string $char): int
    {
      return self::charWidths[$char] ?? 6;
    }
    
    static function centerLine(string $line): string
    {
      return self::centerText($line, 30 * 6);
    }
    
    static function encodeItem(Item $item): string
    {
      /**
       * @var LittleEndianNbtSerializer $nbt
       */
      $nbt = new LittleEndianNbtSerializer();
      return base64_encode($nbt->write(new TreeRoot($item->nbtSerialize())));
    }
    
    static function decodeItem(string $value): ?Item
    {
      /**
       * @var LittleEndianNbtSerializer $nbt
       */
      $nbt = new LittleEndianNbtSerializer();
      try {
        $item = Item::nbtDeserialize($nbt->read(base64_decode($value))->mustGetCompoundTag());
      }catch(SavedDataLoadingException|\Exception $ex) {
        throw new \RuntimeException("Error during decoding of an item, incorrect item: " . $ex->getMessage() . ", data: " . $value);
        return null;
      }
      return $item;
    }
    
    static function effectToArray(EffectInstance $effectInstance, ?int $duration = null): array
    {
      return ["id" => EffectIdMap::getInstance()->toId($effectInstance->getType()), "amplifier" => $effectInstance->getAmplifier(), "duration" => $duration ?? $effectInstance->getDuration()];
    }
    
    static function arrayToEffect(array $data): ?EffectInstance
    {
      if (empty($data["id"]) && empty($data["amplifier"]) && empty($data["duration"])) {
        return null;
      }
      return new EffectInstance(EffectIdMap::getInstance()->fromId($data["id"]), $data["amplifier"], $data["duration"]);
    }
    
    static function convertArmorSlot(int|string $slot): int|string
    {
      if (is_string($slot)) {
        return match(strtolower($slot)) {
				  "helmet" => 0,
			  	"chestplate", "chest" => 1,
		  		"leggings" => 2,				
          "boots" => 3,
			  };
      }
      $int = $slot % 4;
      var_dump($int);
      return match($int) {
        0 => "helmet",
        1 => "chestplate",
        2 => "leggings",
        3 => "boots",
        default => 0
      };
    }
    
    static function positionToArray(Vector3 $position): array
    {
      return ["x" => round($position->x, 2), "y" => round($position->y, 2), "z" => round($position->z, 2)];
    }
    
    /**
     * en mis otros games lo tenia pq no ahora?
     */
    static function maxAndMin(float $first, float $second): array
    {
      return $first > $second ? [$first, $second] : [$second, $first]; //sirve para horizontal
    }
    
    static function fastTeleport(Living $entity, Vector3 $position, ?Vector3 $lookAt = null): void
    {
      [$yaw, $pitch] = self::lookAt($entity, $position, $lookAt);
      $entity->teleport($position, $yaw, $pitch);
      NetworkBroadcastUtils::broadcastPackets([$entity->getViewers()], [MoveActorAbsolutePacket::create($entity->getId(), $entity->getOffsetPosition($location = $entity->getLocation()), $location->pitch, $location->yaw, $location->yaw, (MoveActorAbsolutePacket::FLAG_TELEPORT | ($entity->onGround ? MoveActorAbsolutePacket::FLAG_GROUND : 0)))]);
    }
  
    static function lookAt(Living $entity, Vector3 $pos, ?Vector3 $lookAt = null): array
    {
      if($lookAt === null){
        return [null, null];
      }
      $horizontal = sqrt(($lookAt->x - $pos->x) ** 2 + ($lookAt->z - $pos->z) ** 2);
      $vertical = $lookAt->y - ($pos->y + $entity->getEyeHeight());
      $pitch = -atan2($vertical, $horizontal) / M_PI * 180; //negative is up, positive is down
      
      $xDist = $lookAt->x - $pos->x;
      $zDist = $lookAt->z - $pos->z;

      $yaw = atan2($zDist, $xDist) / M_PI * 180 - 90;
      if($yaw < 0){
        $yaw += 360.0;
      }
		  return [$yaw, $pitch];
    }
    
    static function getViewersForPosition(Player $player): array
    {
      $players = [];
      $world = $player->getWorld();
      $position = $player->getPosition();
      foreach($world->getViewersForPosition($position) as $found) {
        if ($found->canSee($player)) {
          $players[] = $found;
        }
      }
      return $players;
    }

    static function queueOnlyBE(string $device, string $touch): bool
    {
      return !isset(self::NO_QUEUE_BE[$device]) && $touch === "Touch";
    }

    static function calculateElo(int $winner, int $losser)
    {
      $scoreA = 1 / (1 + (pow(10, ($losser - $winner) / 408)));
      $scoreB = abs(1 / (1 + pow(10, ($winner - $losser) / 408)));

      $winnerElo = $winner + intval(32 * (1 - $scoreA));
      $losserElo = $losser + intval(32 * (0 - $scoreB));
      return [
        $winnerElo - $winner,
        abs($losser - $losserElo)
      ];
    }
}