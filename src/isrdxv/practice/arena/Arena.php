<?php
declare(strict_types=1);

namespace isrdxv\practice\arena;

use isrdxv\practice\kit\DefaultKit;

use pocketmine\world\World;

/**
 * These are the functions that should be present when extending the class
 */
abstract class Arena
{
  const TYPE_DUEL = "DUEL";

  const TYPE_FFA = "FFA";

  const TYPE_EVENT = "EVENT";
  
  abstract function getName(): string;
  
  abstract function getWorld(): ?World;
  
  abstract function getKit(): ?DefaultKit;
}