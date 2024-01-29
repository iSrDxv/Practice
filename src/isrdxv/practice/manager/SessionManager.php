<?php

namespace isrdxv\practice\manager;

use isrdxv\practice\session\Session;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class SessionManager
{
    use SingletonTrait;
    
    private array $sessions = [];

    function set(Player|string $value): bool
    {
    	$player = ($value instanceof Player) ? $value->getName() : $value;
    	if (isset($this->sessions[$player])) {
           return false;
    	}
      $this->sessions[$player] = new Session($player);
       return true;
    }

    function get(Player|string $value): Session
    {
        $player = ($value instanceof Player) ? $value->getName() : $value;
        if (isset($this->sessions[$player])) {
            return $this->sessions[$player];
        }
       $this->sessions[$player] = new Session($player);
        return $this->sessions[$player];
    }

    function all(): array
    {
        return $this->sessions;
    }

    function reload(): void
    {
        $sessions = [];
        foreach($this->sessions as $name => $value) {
            $sessions[$name] = new Session($name);
        }
        $this->sessions = $sessions;
    }
}