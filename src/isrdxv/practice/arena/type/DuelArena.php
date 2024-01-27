<?php
declare(strict_types=1);

namespace isrdxv\practice\arena;

use isrdxv\kit\DefaultKit;

use pocketmine\math\Vector3;
use pocketmine\world\World;

final class DuelArena
{
  private string $name;
  
  private World $world;
  
  private DefaultKit $kit;
  
  private Vector3 $spawn1;
  
  private Vector3 $spawn2;
  
  /**
   * At what maximum height can you build?
   */
  private int $maxHeight;
  
  function getName(): string
  {
    return $this->name;
  }
  
  function getWorld(): ?World
  {
    return $this->world;
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
  
  function canBuild(): bool
  {
    return true;
  }
  
  function extract(): array
  {
    return [];
  }
}