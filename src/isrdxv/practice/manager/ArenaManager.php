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
      $arena = $this->load(basename($file, ".json"), json_decode(file_get_contents($this->defaultPath . $file), true));
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
        case Arena::FFA:
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
        case Arena::DUEL:
          
        break;
      }
    }
  }
  
  function create(string $name, string $type, World $world, DefaultKit $kit): bool
  {
    if (!isset($this->duels[$name], $this->ffa[$name]) && empty($this->get($name))) {
      if ($type === Arena::FFA) {
        $arena = ($this->ffa[$name] = new FFArena($name, $kit, $world, [1 => $world->getSpawnLocation()]));
        $this->save($arena);
        return true;
      }
      if ($type === Arena::DUEL) {
        $worldName = $world->getFolderName();
        $spawn = $world->getSpawnLocation();
        $arena = ($this->duels[$name] = new DuelArena($name, $kit, $worldName, $spawn, $spawn, 255));
        $this->save($arena);
        return true;
      }
    }
    return false;
  }
  
  function get(string $name): ?Arena
  {
    return $this->ffa[$name] ?? $this->duels[$name] ?? null;
  }
  
  function all(): array
  {
    return array_merge($this->duels, $this->ffa);
  }
  
  function save(Arena $arena): void
	{
		if(!file_exists($filePath = $this->defaultPath . "{$arena->getName()}.json")){
			fclose(fopen($filePath, "w"));
		}
		file_put_contents($filePath, json_encode($kit->export()));
	}
	
}