<?php
declare(strict_types=1);

namespace isrdxv\practice\party;

use isrdxv\practice\Practice;

use pocketmine\player\Player;

class Party
{
  private string $owner;
  
  private string $name;
  
  private bool $open;
  
  private string $code;
  
  private array $players = [];
  
  private array $blackList = [];
  
  function __construct(string $owner, string $name, string $code, bool $open = true)
  {
    $this->owner = $owner;
    $this->name = $owner . "'s Party";
    $this->code = $code;
    //bin2hex(random_bytes(3))
    array_push($this->players, $owner);
    
    $this->open = $open;
  }
  
  function getName(): string
  {
    return $this->name;
  }
  
  function getOwner(): string
  {
    return $this->owner;
  }
  
  function getPlayer(string $name): ?Player
  {
    if (in_array($name, $this->players, true)) {
      return Server::getInstance()->getPlayerExact($this->players[array_search($name, $this->players, true)]);
    }
    return null;
  }
  
  function getPlayers(): array
  {
    return $this->players;
  }
  
  function getBlacklist(): array
  {
    return $this->blacklist;
  }
  
  function isOpen(): bool
  {
    return $this->open === true;
  }
  
  function isOwner(string $name): bool
  {
    return $this->owner === $name;
  }
  
  function isMember(Player|string $player): bool
  {
    $value = $player instanceof Player ? $player->getName() : $player;
    return isset($this->players[array_search($value, $this->players, true)]);
  }
  
  function equals(Party $party): bool
  {
    return $party->getName() === $this->name;
  }
  
  function open(bool $value): void
  {
    $this->open = $value;
  }
  
  function __destruct()
  {
    Server::getInstance()->getLogger()->warning("Data of this class is about to be destroyed: " . get_class($this));
  }
  
}