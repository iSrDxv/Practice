<?php
declare(strict_types=1);

namespace isrdxv\practice\manager;

use isrdxv\practice\{
  PracticeLoader
};
use isrdxv\practice\arena\Arena;
use isrdxv\practice\kit\DefaultKit;

use pocketmine\world\World;
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
    $this->defaultPath = PracticeLoader::getInstance()->getDataFolder() . "arenas";
    foreach(glob($this->defaultPath . DIRECTORY_SEPARATOR . "*.json")  as $file) {
      if (!is_file($file) || str_ends_with($file, ".json") === false) {
        continue;
      }
      $arena = $this->load(basename($file, ".json"), json_decode(file_get_contents($file), true));
      if ($arena instanceof FFAArena) {
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
          
        break;
        case Arena::DUEL:
          
        break;
      }
    }
  }
  
  function create(string $name, string $type, World $world, DefaultKit $kit): bool
  {
    
  }
  
  function get(string $name): ?Arena
  {
    return $this->ffa[$name] ?? $this->duels[$name] ?? null;
  }
  
  function all(): array
  {
    return array_merge($this->duels, $this->ffa);
  }
  
}