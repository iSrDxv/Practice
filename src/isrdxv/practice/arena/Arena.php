<?php
declare(strict_types=1);

namespace isrdxv\practice\arena;

use pocketmine\world\World;

abstract class Arena
{
  
  abstract function getName(): string;
  
  abstract function getWorld(): ?World;
  
  abstract function getKit(): ?DefaultKit;
  
  abstract function getGameModeIcon(): string;
  
  abstract function extract(): array;
}