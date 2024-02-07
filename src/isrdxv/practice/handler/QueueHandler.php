<?php
declare(strict_types=1);

namespace isrdxv\practice\handler;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\utils\Time;

use pocketmine\player\Player;
use pocketmine\utils\{
  TextFormat,
  SingletonTrait
};
use pocketmine\scheduler\ClosureTask;

final class QueueHandler
{
  private array $queues = [];
  
  function __construct()
  {
    self::setInstance($this);
  }
  
}