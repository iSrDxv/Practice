<?php
declare(strict_types=1);

namespace isrdxv\practice\arena\type;

use isrdxv\practice\Practice;
use isrdxv\practice\kit\DefaultKit;
use isrdxv\practice\arena\Arena;
use isrdxv\practice\manager\{
  ArenaManager,
  KitManager,
  SessionManager
};

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\world\World;

final class FFArena extends Arena
{
  private string $name;
  
  private string $kit;
  
  private int $world; 
  
  private array $spawns;
  
  private array $players = [];
  
  private array $spectators = [];
  
  function __construct(string $name, DefaultKit $kit, World $world, array $spawns)
  {
    $this->name = $name;
    $this->kit = $kit->getName();
    $this->world = $world->getId();
    $this->spawns = $spawns;
  }
  
  function getName(): string
  {
    return $this->name;
  }
  
  function getKit(): ?DefaultKit
  {
    return KitManager::getInstance()->getKit($this->kit);
  }
  
  function setKit(DefaultKit $kit): void
  {
    $this->kit = $kit->getName();
  }
  
  function getIcon(): string
  {
    return $this->getKit()?->getDataInfo()->getIcon() ?? "";
  }
  
  function getWorld(): ?World
  {
    return Server::getInstance()->getWorldManager()->getWorld($this->world);
  }
  
  function addPlayer(Player $player): void
  {
    if (($world = $this->getWorld()) !== null && ($session = SessionManager::getInstance()->get($player)) !== null) {
      $position = Position::fromObject($this->spawns[array_rand($this->spawns)], $world);
    }
  }
  
  function removePlayer(string $name): bool
  {
    if (isset($this->players[$name])) {
      unset($this->players[$name]);
      return true;
    }
    return false;
  }
  
  function getPlayers(): array
  {
    return $this->players ?? [];
  }
  
  function isPlayer(Player|string $player): bool
  {
    return isset($this->players[$player instanceof Player ? $player->getName() : $player]);
  }
  
  function addSpectator(): void
  {}
  
  function removeSpectator(string $name): void
  {
    if (isset($this->spectators[$name])) {
      unset($this->spectators[$name]);
      return true;
    }
    return false;
  }
  
  function export(): array
  {
    $spawns = [];
    foreach($this->spawns as $num => $value) {
      $spawns[$num] = Practice::positionToArray($value);
    }
    return ["type" => Arena::FFA, "kit" => $this->kit?->getName(), "world" -> $this->getWorld()?->getFolderName(), "spawns" => $spawns];
  }
}