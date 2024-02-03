<?php
declare(strict_types=1);

namespace isrdxv\practice\party\invite;

use isrdxv\practice\Practice;
use isrdxv\practice\party\invite\PartyInvite;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\{
  TextFormat,
  SingletomTrait
};

class PartyInviteManager
{
  use SingletonTrait;
  
  private array $invites = [];
  
  function send(Player $to, Player $from, Party $party): void
  {
    if ($from->isOnline()) {
      $from->sendMessage(Practice::SERVER_PREFIX . TextFormat::GRAY . "Successfully sent party invite to " . $to->getDisplayName());
    }
    $key = $from->getName() . ":" . $to->getName();
    $invites = array_filter($this->getOf($to->getName()), fn($inv) => $inv !== []);
    var_dump($invites);
    foreach($invites as $invite) {
      if (!$this->isInvited($key) or $invite->getParty() !== strval($party)) {
        if ($to->isOnline()) {
        $to->sendMessage(Practice::SERVER_PREFIX . TextFormat::GRAY . "Received a new party invite from " . $to->getDisplayName());
        }
      }
    }
    $this->invites[$key] = new PartyInvite($from, $to, $party);
  }
  
  function accept(PartyInvite $invite): void
  {
    $from = Server::getInstance()->getPlayerExact($invite->getFrom());
    $to = Server::getInstance()->getPlayerExact($invite->getTo());
    if ($from instanceof Player && $to instanceof Player) {
      $from->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . $to->getDisplayName() . "'s " . TextFormat::GRAY . "has accepted the invitation to your party");
      $to->sendMessage(Practice::SERVER_PREFIX . TextFormat::GRAY . "You have accepted " . TextFormat::GREEN . $from->getDisplayName() . "'s " . TextFormat::GRAY . "party invite");
      unset($this->invites[$from->getName() . ":" . $to->getName()]);
    }
  }
  
  function isInvited(string $identifier): bool
  {
    return ($this->invites[$identifier] !== null) ? true : false;
  }
  
  function getOf(string $name): array
  {
    $invites = [];
    foreach($this->invites as $key => $class) {
      if ($class->getTo() === $name) {
        if (Server::getInstance()->getPlayerExact($from = $class->getFrom()) !== null) {
          $invites[$from] = $class;
        } else {
          unset($this->invites[$name]);
        }
      }
    }
    return $invites;
  }
  
  function deleteOf(string $name): void
  {
    foreach($this->invites as $key => $class) {
      if ($class->getTo() == $name or $class->getFrom() == $name) {
        unset($this->invites[$key]);
      }
    }
  }
  
}