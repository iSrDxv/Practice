<?php
declare(strict_types=1);

namespace isrdxv\practice\handler;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\session\Session;
use isrdxv\practice\handler\DuelHandler;
use isrdxv\practice\duel\queue\UserQueued;
use isrdxv\practice\manager\SessionManager;
use isrdxv\practice\utils\Time;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\{
  TextFormat,
  SingletonTrait
};
use pocketmine\scheduler\ClosureTask;

final class QueueHandler
{
  use SingletonTrait;

  /**
   * @var UserQueued[] $queue
   * @iSrDxv
   */
  private array $queues = [];
  
  function __construct()
  {
    self::setInstance($this);
  }
  
  function get(string $name): ?UserQueued
  {
    return $this->queues[$name] ?? null;
  }

  function all(): array
  {
    return $this->queues;
  }

  function add(Player $player, string $kit, bool $ranked = false): void
  {
    $this->remove($player->getName());
    $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GRAY . "You have joined the queue for " . Practice::SERVER_COLOR . ($ranked ? "Ranked" : "UnRanked") . " " . $kit);
    $this->queues[$name = $player->getName()] = new UserQueued($name, $kit, $ranked);
    if (($opponent = $this->findOpponent($this->queues[$name])) !== null) {
      $this->remove($name);
      $this->remove($opponentName = $opponent->getName());
      DuelHandler::getInstance()->putInDuel($player, Server::getInstance()->getPlayerExact($opponentName), $kit, $ranked);
    } else {
      $this->remove($player->getName());
    }
  }

  function findOpponent(UserQueued $queue): ?UserQueued
  {
    foreach($this->queues as $name => $queued) {
      if ($queued->getName() === $queue->getName()) {
        continue;
      }
      if ($queue->dataEquals($queued)) {
        return $queued;
      }
    }
    return null;
  }

  function remove(string $name, bool $message = false): void
  {
    if (($queue = $this->get($name)) !== null) {
      unset($this->queues[$name]);
      if ($message && ($player = Server::getInstance()->getPlayerExact($name)) !== null) {
        $player?->sendMessage(Practice::SERVER_PREFIX . TextFormat::GRAY . "You have left the queue for " . Practice::SERVER_COLOR . ($queue->isRanked() ? "Ranked" : "UnRanked") . " " . $queue->getKit());
      }
    }
  }

  function getPlayersOfKit(string $kit, bool $ranked): int
  {
    $players = 0;
    foreach($this->all() as $queue) {
      if ($queue->getKit() === $kit && $queue->isRanked() === $ranked) {
        $players += 1;
      }
    }
    return $players;
  }

  function getQueueCount(): int
  {
    return count($this->queues);
  }
}