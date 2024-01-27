<?php

namespace isrdxv\practice;

use isrdxv\practice\PracticeLoader;

use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\item\{
  Item,
  enchantment\EnchantmentInstance
};
use pocketmine\entity\effect\EffectInstance;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\utils\TextFormat;
pocketmine\world\format\io\GlobalItemDataHandlers;
use pocketmine\data\bedrock\{
  EffectIdMap,
  EnchantmentIdMap
};

class Practice
{
    static bool $maintenance = false;
    
    const RANK_SUPERIORS = ["Owner", "Admin", "Mod", "Developer", "Strom", "Zodiac", "YouTuber", "Streamer"];
    
    const BYPASS = ["SrClauYT", "StyleMH"];
    
    const SERVER_NAME = TextFormat::DARK_AQUA . "StromMC";
    
    const SERVER_COLOR = TextFormat::DARK_AQUA;
    
    const SERVER_MOTD = self::SERVER_NAME . ": " . TextFormat::BOLD . TextFormat::GOLD . "¡¡NEW RELEASE!!" . TextFormat::GRAY;

    const SERVER_MAX_PLAYERS = 2; //100

    const SERVER_PREFIX = self::SERVER_NAME . TextFormat::BOLD . TextFormat::GRAY . " » " . TextFormat::RESET;
    
    const BROADCAST_LIST = [
         TextFormat::RESET . TextFormat::GRAY . "Don't forget to enter our store to see the benefits: strommc.tebex.io",
         TextFormat::RESET . TextFormat::GREEN . "Don't forget to enjoy our server, and vote to get a rank for 1 month: link"
    ];
 
    static function setMaintenance(): void
    {
      self::$maintenance = PracticeLoader::getInstance()->getConfig()->getNested("maintenance");
    }
    
    static function getMaintenance(): bool
    {
      return self::$maintenance;
    }
    
    static function itemToArray(Item $item): array
    {
      $data = [];
      /**
       * @var SavedItemStackData
       */
      $itemData = GlobalItemDataHandlers::itemSerializer()->serializeStack($item);
      $data['id'] = $item->getTypeId();
      $data["damage"] = $itemData->getTypeData()->getMeta();
      $data['count'] = $itemData->getCount();
      $data["nbt"] = ($item->hasNamedTag() ? "0x" . base64_encode((new LittleEndianNbtSerializer())->write(new TreeRoot($item->getNamedTag()))) : "");
      $enchantments = [];
      if ($item->hasEnchantments()) {
        foreach($item->getEnchantments() as $enchantment) {
          $enchantments[] = ["id" => EnchantmentIdMap::getInstance()->toId($enchantment->getType()), "level" => $enchantment->getLevel()];
        }
        $data["enchants"] = $enchantments;
      }
      return $data;
    }
    
    static function arrayToItem(array $data): ?Item
    {
      if (empty($data['id']) && empty($data['damage']) && empty($data['count']))
      {
        return null;
      }
      $item = Item::legacyJsonDeserialize($data);
      var_dump($item);
      if (empty($item)) {
        return null;
      }
      if (isset($data["enchants"])) {
        foreach($data["enchants"] as $enchantment) {
          if (empty($enchantment["id"]) && empty($enchantment["level"])) {
            continue;
          }
          $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantment["id"]), $enchantment["level"]));
        }
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
    
}