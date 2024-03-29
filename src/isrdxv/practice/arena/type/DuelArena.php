<?php
declare(strict_types=1);

namespace isrdxv\practice\arena\type;

use isrdxv\practice\kit\DefaultKit;
use isrdxv\practice\Practice;
use isrdxv\practice\arena\Arena;

use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\world\World;

final class DuelArena extends Arena
{
  private string $name;
  
  private int $world;
  
  private string $worldName;
  
  private DefaultKit $kit;
  
  private Vector3 $spawn1;
  
  private Vector3 $spawn2;
  
  private bool $enabled;
  
  /**
   * At what maximum height can you build?
   */
  private int $maxHeight;
  
  function __construct(string $name, DefaultKit $kit, string $world, Vector3 $spawn1, Vector3 $spawn2, int $maxHeight, bool $enabled = true)
  {
    $this->name = $name;
    $this->kit = $kit;
    $this->world = Server::getInstance()->getWorldManager()->getWorldByName($world)->getId() ?? 0;
    $this->worldName = $world;
    $this->spawn1 = $spawn1;
    $this->spawn2 = $spawn2;
    $this->maxHeight = $maxHeight;
    $this->enabled = $enabled;
  }
  
  function getName(): string
  {
    return $this->name;
  }
  
  function getWorld(): ?World
  {
    return Server::getInstance()->getWorldManager()->getWorld($this->world);
  }
  
  function getWorldName(): string
  {
    return $this->worldName;
  }
  
  function getKit(): ?DefaultKit
  {
    return $this->kit;
  }
  
  function getSpawn1(): Vector3
  {
    return $this->spawn1;
  }
  
  function getSpawn2(): Vector3
  {
    return $this->spawn2;
  }
  
  function getMaxHeight(): int
  {
    return $this->maxHeight;
  }
  
  function isEnabled(): bool
  {
    return $this->enabled;
  }
  
  function canBuild(Vector3 $position): bool
  {
    if ($position->y >= $this->maxHeight) {
      return false;
    }
    return true;
  }
  
  function extract(): array
  {
    $spawns = [];
    $num = 0;
    $num++;
    if ($num === 1) {
      $spawns[$num] = Practice::positionToArray($this->spawn1);
    }else{
      $spawns[$num] = Practice::positionToArray($this->spawn2);
    }
    return ["type" => Arena::TYPE_DUEL, "world" => $this->worldName, "kit" => $this->kit?->getName(), "spawns" => $spawns, "maxHeight" => $this->maxHeight, "enabled" => $this->enabled];
  }
  
}