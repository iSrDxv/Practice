<?php

namespace isrdxv\practice\kit;

use isrdxv\practice\kit\misc\{
  KitDataInfo,
  KnockbackInfo
};
use isrdxv\practice\Practice;

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
  
  function __construct(string $name, array $inventory, array $armor, KitDataInfo $dataInfo, KnockbackInfo $kbInfo)
  {
    $this->name = $name;
    $this->mainName = strtolower($name);
    $this->inventory = $inventory;
    $this->armor = $armor;
    $this->dataInfo = $dataInfo;
    $this->kbInfo = $kbInfo;
  }
  
  function giveTo(Player $player): bool
  {
    $player->getInventory()->setContents($this->inventory);
    $player->getArmorInventory()->setContents($this->armor);
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
    return $this->kitInfo;
  }
  
  function setInventory(array $items): array
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
    return ["name" => $this->name, "inventory" => $items, "armor" => $armor, "data" => $this->dataInfo->export(), "kb" => $this->kbInfo->export()];
  }
  
}