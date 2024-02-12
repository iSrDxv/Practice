<?php
declare(strict_types=1);

namespace isrdxv\practice\manager;

use isrdxv\practice\{
  PracticeLoader
};
use isrdxv\practice\arena\Arena;
use isrdxv\practice\arena\type\{
  FFArena,
  DuelArena
};
use isrdxv\practice\manager\KitManager;
use isrdxv\practice\kit\DefaultKit;

use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\utils\Filesystem;
use pocketmine\utils\SingletonTrait;

use function glob;
use function is_file;
use function str_ends_with;
use function basename;
use function json_decode;
use function file_get_contents;

final class ArenaManager
{
  use SingletonTrait;
  
  private array $duels = [];
  
  private array $ffa = [];
  
  private string $defaultPath;
  
  function init(): void
  {
    $this->defaultPath = PracticeLoader::getInstance()->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR;
    foreach(glob($this->defaultPath . "*.json")  as $file) {
      if (!is_file($file) || str_ends_with($file, ".json") === false) {
        continue;
      }
      /**
       * NOTE: load the files of folder /arenas
       */
      $arena = $this->load(basename($file, ".json"), json_decode(Filesystem::fileGetContents($file), true, flags: JSON_THROW_ON_ERROR));
      if ($arena instanceof FFArena) {
        $this->ffa[$arena->getName()] = $arena;
      }elseif ($arena instanceof DuelArena) {
        $this->duels[$arena->getName()] = $arena;
      }
    }
  }
  
  function load(string $name, array $data): ?Arena
  {
    if ($data["type"] !== null) {
      switch($data["type"]) {
        case Arena::TYPE_FFA:
          if (isset($data["world"], $data["spawns"], $data["kit"])) {
            if (($kit = KitManager::getInstance()->get($data["kit"])) !== null) {
              $wm = Server::getInstance()->getWorldManager();
              if (!$wm->isWorldLoaded($data["world"])) {
                $wm->loadWorld($data["world"]);
              }
              if (($world = $wm->getWorldByName($data["world"])) !== null) {
                $world->setTime(0);
                $world->stopTime();
                
                $spawns = [];
                foreach($data["spawns"] as $num => $value) {
                  $spawns[$num] = new Vector3($value["x"], $value["y"], $value["z"]);
                }
                return new FFArena($name, $kit, $world, $spawns);
              }
            }
          }
        break;
        case Arena::TYPE_DUEL:
          
        break;
      }
    }
  }
  
  function create(string $name, string $type, World $world, DefaultKit $kit): bool
  {
    if (!isset($this->duels[$name], $this->ffa[$name]) && empty($this->get($name))) {
      if ($type === Arena::TYPE_FFA) {
        $arena = ($this->ffa[$name] = new FFArena($name, $kit, $world, [1 => $world->getSpawnLocation()]));
        $this->save($arena);
        return true;
      }
      if ($type === Arena::TYPE_DUEL) {
        $worldName = $world->getFolderName();
        $spawn = $world->getSpawnLocation();
        $arena = ($this->duels[$name] = new DuelArena($name, $kit, $worldName, $spawn, $spawn, 255));
        $this->save($arena);
        return true;
      }
    }
    return false;
  }
  
  function getRandomArena(string $kit): ?Arena
  {
    $result = [];
    foreach($this->duels as $duel) {
      if ($duel->getWorld() === null || !$duel->isEnabled()) {
        continue;
      }
      if ($duel->getKit() !== null && $duel->getKit()?->getName() === $kit) {
        $result[] = $duel;
      }
    }
    return empty($result) ? null : $result[array_rand($result)];
  }

  function get(string $name): ?Arena
  {
    return $this->ffa[$name] ?? $this->duels[$name] ?? null;
  }
  
  function all(): array
  {
    return array_merge($this->duels, $this->ffa);
  }

  function unset($name): void
  {
    if (($ffa = $this->ffa[$name]) !== null) {
      $ffa->destroy();
      unset($ffa);
      return;
    }
    if (($duel = $this->duels[$name]) !== null) {
      $duel->destroy();
      unset($duel);
      return;
    }
  }

  function save(Arena $arena): void
	{
		if(!file_exists($filePath = $this->defaultPath . "{$arena->getName()}.json")){
			Filesystem::safeFilePutContents($filePath, json_encode([], JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR));
		}
		Filesystem::safeFilePutContents($filePath, json_encode($arena, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR));
	}

  function delete(string $name): bool
  {
    if (($arena = $this->get($name)) === null) {
      return false;
    }
    if (!is_file($this->defaultPath . $arena->getName() . ".json")) {
      return false;
    }
    $this->unset($arena->getName());
    return true;
  }
}