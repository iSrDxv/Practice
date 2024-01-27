<?php
declare(strict_types=1);

namespace isrdxv\practice\arena;

use pocketmine\world\World;

final class DuelArena
{
  private string $name;
  
  private World $world;
  
  private DefaultKit $kit;
  
  private string $gameIcon;

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
  
  function getGameModeIcon(): string
  {
    return $this->gameIcon;
  }
  
  function extract(): array
  {
    return [];
  }
}