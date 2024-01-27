<?php
declare(strict_types=1);

namespace isrdxv\practice\arena;

use isrdxv\kit\DefaultKit;

use pocketmine\world\World;

/**
 * These are the functions that should be present when extending the class
 */
abstract class Arena
{
  
  abstract function getName(): string;
  
  abstract function getWorld(): ?World;
  
  abstract function getKit(): ?DefaultKit;
  
  abstract function extract(): array;
}