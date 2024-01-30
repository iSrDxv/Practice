<?php
declare(strict_types=1);

namespace isrdxv\practice\duel\world;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};

use exodus\worldbackup\WorldBackup;

use pocketmine\Server;
use pocketmine\world\World;

use Exception;

class DuelWorld
{
  private World $originalWorld;
  
  private string $original;
  
  private string $id;
  
  private int $worldId;
  
  function __construct(World $world)
  {
    $this->originalWorld = $world;
    $this->original = $world->getId();
    $this->id = Practice::getRandomId();
    try {
      WorldBackup::createBackup($this->id, $world->getFolderName());
    }catch(Exception $exception) {
      PracticeLoader::getInstance()->getLogger()->error($exception->getMessage());
    }
    $this->worldId = (Server::getInstance()->getWorldManager()->getWorldByName($this->id))->getId();
  }
  
  /**
   * NOTE: actually this is the original world
   */
  function getWorld(): ?World
  {
    return $this->originalWorld;
  }
  
  function getCopyWorld(): ?World
  {
    return Server::getInstance()->getWorldManager()->getWorld($this->worldId);
  }
  
}