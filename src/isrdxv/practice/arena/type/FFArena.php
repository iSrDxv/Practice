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
use JsonSerializable;

use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\World;

final class FFArena extends Arena implements JsonSerializable
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
    return KitManager::getInstance()->get($this->kit) ?? null;
  }
  
  function setKit(DefaultKit $kit): void
  {
    $this->kit = $kit->getName();
    ArenaManager::getInstance()->save($this);
  }
  
  function getIcon(): string
  {
    return $this->getKit()?->getDataInfo()->icon ?? "";
  }
  
  function getWorld(): ?World
  {
    return Server::getInstance()->getWorldManager()->getWorld($this->world);
  }
  
  function getSpawn(int $num = 0): array
  {
    return ($num === 0) ? $this->spawns : [$this->spawns[$num] ?? []];
  }
  
  function addSpawn(int $num, Vector3 $spawn): void
  {
    if (isset($this->spawns[$num])) {
      $this->spawns[$num] = $spawn;
    }elseif ($num === 0) {
      $this->spawns[count($this->spawns) + 1] = $spawn;
    }
    ArenaManager::getInstance()->save($this);
  }
  
  function removeSpawn(int $num = 0): void
  {
    if (isset($this->spawns[$num])) {
      unset($this->spawns[$num]);
      $spawns = $this->spawns;
      $this->spawns = [];
      $num = 1;
      foreach($spawns as $spawn) {
        $this->spawns[$num++] = $spawn;
      }
      ArenaManager::getInstance()->save($this);
    }
  }
  
  function addPlayer(Player $player): void
  {
    if (($world = $this->getWorld()) !== null && ($session = SessionManager::getInstance()->get($player)) !== null) {
      $position = Position::fromObject($this->spawns[array_rand($this->spawns)], $world);
      if (($player = $session?->getPlayer()) !== null) {
        $this->players[$player->getName()] = $player;
        $player->teleport($position);
        $this->getKit()?->giveTo($player);
      }
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
  
  function addSpectator(Player $player): void
  {
    if (($world = $this->getWorld()) !== null && ($session = SessionManager::getInstance()->get($player)) !== null) {
      $position = Position::fromObject($this->spawns[array_rand($this->spawns)], $world);
    }
  }
  
  function removeSpectator(string $name): bool
  {
    if (isset($this->spectators[$name])) {
      unset($this->spectators[$name]);
      return true;
    }
    return false;
  }
  
  function isSpectator(Player|string $player): bool
  {
    return isset($this->spectators[$player instanceof Player ? $player->getName() : $player]);
  }
  
  function jsonSerialize(): mixed
  {
    $spawns = [];
    foreach($this->spawns as $num => $value) {
      $spawns[$num] = Practice::positionToArray($value);
    }
    return ["type" => Arena::TYPE_FFA, "kit" => $this->kit, "world" => $this->getWorld()?->getFolderName(), "spawns" => $spawns];
  }
  
}