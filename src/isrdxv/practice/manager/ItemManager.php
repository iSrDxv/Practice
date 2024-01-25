<?php
declare(strict_types=1);

namespace isrdxv\practice\manager;

use isrdxv\practice\Practice;
use isrdxv\practice\item\PluginItem;

use pocketmine\item\{
  Item,
  VanillaItems
};
use pocketmine\block\{
  VanillaBlocks,
  utils\MobHeadType,
  utils\DyeColor
};
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class ItemManager
{
  
  //LOCAL NAMES
  const DUEL = "lobby_duel";
  const FFA = "lobby_ffa";
  const PARTY = "lobby_party";
  const SPECTATE = "lobby_spec_user";
  const SHOP = "lobby_shop_menu";
  const PROFILE = "lobby_profile";
  const LEAVE_QUEUE = "lobby_leave";
  
  const PARTY_DUEL = "p_duel";
  const PARTY_SETTINGS = "p_settings";
  
  private static array $lobby = [];
  
  private static array $party = [];
  
  static function init(): void
  {
    self::register(0, self::DUEL, VanillaItems::DIAMOND_SWORD()->setCustomName(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . Practice::SERVER_COLOR . "Duels" . TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GRAY . " «"));
    self::register(1, self::FFA, VanillaItems::IRON_SWORD()->setCustomName(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . Practice::SERVER_COLOR . "Free For All" . TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GRAY . " «"));
    self::register(4, self::PARTY, VanillaItems::NETHER_STAR()->setCustomName(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . Practice::SERVER_COLOR . "Party" . TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GRAY . " «"));
    self::register(5, self::SPECTATE, VanillaItems::COMPASS()->setCustomName(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . Practice::SERVER_COLOR . "Spectator Menu" . TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GRAY . " «"));
    self::register(6, self::SHOP, VanillaItems::BOOK()->setCustomName(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . Practice::SERVER_COLOR . "Shop Menu" . TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GRAY . " «"));
    self::register(7, self::LEAVE_QUEUE, VanillaItems::DYE()->setColor(DyeColor::RED())->setCustomName(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . Practice::SERVER_COLOR . "Leave Queue" . TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GRAY . " «"));
    self::register(9, self::PROFILE, VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::PLAYER())->asItem()->setCustomName(TextFormat::BOLD . TextFormat::DARK_GRAY . "» " . TextFormat::RESET . Practice::SERVER_COLOR . "Player Menu" . TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_GRAY . " «"));
  }
  
  /**
   * @param int $slot
   * The slot in which it will be in the inventory
   * @param string $localName
   * the name to verify the interaction
   * @param Item $item
   * the item you will have
   */
  static function register(int $slot, string $localName, Item $item): void
  {
    $item = new PluginItem($slot, $item->setNamedTag($item->getNamedTag()->setString("Practice", $localName)));
    if (str_contains($localName, "lobby_")) {
      self::$lobby[$localName] = $item;
    } elseif (str_contains($localName, "p_")) {
      self::$party[$localName] = $item;
    }
  }
  
  static function spawnLobbyItems(Player $player): void
  {
    $inventory = $player->getInventory();
    $inventory->clearAll();
    $player->getArmorInventory()->clearAll();
    $items = array_keys(self::$lobby);
    foreach($items as $localName) {
      if ($localName === self::LEAVE_QUEUE) {
        continue;
      }
      $item = self::$lobby[$localName];
      $inventory->setItem($item->getSlot(), $item->getItem());
    }
  }
  
}