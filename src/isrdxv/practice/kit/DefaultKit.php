<?php

namespace isrdxv\practice\kit;

use isrdxv\practice\kit\misc\{
  KitDataInfo,
  KnockbackInfo,
  EffectsData
};
use isrdxv\practice\Practice;
use isrdxv\practice\utils\Time;

use pocketmine\item\{
  Item,
  VanillaItems
};
use pocketmine\player\Player;

use function strtolower;

final class DefaultKit implements Kit
{
  private string $name;
  
  private string $mainName;
  
  /** @var Item[] **/
  protected array $inventory = [];
  
  /** @var Item[] **/
  protected array $armor = [];
  
  protected KitDataInfo $dataInfo;
  
  protected KnockbackInfo $kbInfo;
  
  protected EffectsData $effects;
  
  function __construct(string $name, array $inventory, array $armor, KitDataInfo $dataInfo, KnockbackInfo $kbInfo, EffectsData $effects)
  {
    $this->name = $name;
    $this->mainName = strtolower($name);
    $this->inventory = $inventory;
    $this->armor = $armor;
    $this->dataInfo = $dataInfo;
    $this->kbInfo = $kbInfo;
    $this->effects = $effects;
  }
  
  function giveTo(Player $player): bool
  {
    $player->getInventory()->setContents($this->inventory);
    $player->getArmorInventory()->setContents($this->armor);
    $effectManager = $player->getEffects();
    foreach($this->effects->all() as $effect) {
      $effectManager->add($effect->setDuration(Time::minutesToTicks(60))->setVisible(false));
    }
    return true;
  }
  
  function getName(): string
  {
    return $this->name;
  }
  
  function getMainName(): string
  {
    return $this->mainName;
  }
  
  function getInventory(): array
  {
    return $this->inventory;
  }
  
  function getArmorInventory(): array
  {
    return $this->armor;
  }
  
  function getDataInfo(): KitDataInfo
  {
    return $this->dataInfo;
  }
  
  function getKnockbackInfo(): KnockbackInfo
  {
    return $this->kbInfo;
  }
  
  function getEffectsData(): EffectsData
  {
    return $this->effects;
  }
  
  function setInventory(array $items): void
  {
    $this->inventory = $items;
  }
  
  function setArmorInventory(array $armor): void
  {
    $this->armor = $armor;
  }
  
  function equals($kit): bool
  {
    if ($kit instanceof Kit) {
      return $this->mainName === $kit->getMainName();
    }
    return false;
  }
  
  function extract(): array
  {
    $items = [];
    $armor = [];
    foreach($this->inventory as $slot => $item) {
      if ($item === VanillaItems::AIR()) {
        continue;
      }
      $items[] = Practice::encodeItem($item);
    }
    foreach($this->armor as $slot => $item) {
      if ($item === VanillaItems::AIR()) {
        continue;
      }
      $armor[] = Practice::encodeItem($item);
    }
    return ["name" => $this->name, "inventory" => $items, "armor" => $armor, "data" => $this->dataInfo->export(), "kb" => $this->kbInfo->export(), "effects" => $this->effects->export()];
  }
  
}