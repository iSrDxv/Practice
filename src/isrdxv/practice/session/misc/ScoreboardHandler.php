<?php

namespace isrdxv\practice\session\misc;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\handler\DuelHandler;
use isrdxv\practice\handler\QueueHandler;
use isrdxv\practice\session\Session;
use isrdxv\practice\manager\{
  SessionManager,
  TaskManager
};

use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

use exodus\scoreboard\Scoreboard;

use IvanCraft623\RankSystem\session\SessionManager as SessionRank;

class ScoreboardHandler
{
  const TYPE_LOBBY = "lobby";
  const TYPE_DUEL = "duel";
  const TYPE_FFA = "ffa";
  const TYPE_PARTY = "party";
  
  private Player $player;
  
  private ?string $type;
 
  private string $id = "";
 
  private string $line;
  
  private ?Scoreboard $scoreboard;
  
  function __construct(Player $player)
  {
    $this->player = $player;
    $this->type = null;
    $this->line = "";
  }
  
  function getType(): ?string
  {
    return $this->type ?? null;
  }
  
  function setScoreboard(?string $type = null): void
  {
    if (($session = SessionManager::getInstance()->get($this->player)) !== null && $session->getSetting("scoreboard") !== false) {
      if (empty($type)) {
        $this->scoreboard->remove();
        $this->scoreboard = null;
        TaskManager::getInstance()->delete($this->id);
        $this->id = "";
      }
      $this->scoreboard = $this->scoreboard ?? Scoreboard::create($this->player, TextFormat::BOLD . Practice::SERVER_COLOR . "PRACTICE" . TextFormat::GRAY . " (NA)");
      $this->type = null;
      switch($type){
        case self::TYPE_LOBBY:
          $this->setLobby($session);
        break;
      }
    }
  }
  
  function setLobby(Session $session): void
  {
    if (($task = TaskManager::getInstance()->get($this->id)) !== null) {
      TaskManager::getInstance()->delete($this->id);
    }
    $line = 0;
    $this->type = self::TYPE_LOBBY;
    $this->id = TaskManager::getInstance()->set(new ClosureTask(function() use($session, $line): void {
      if (!$session->getPlayer()?->isOnline()) {
        return;
      }
      $this->scoreboard?->spawn();
      $this->scoreboard?->setLine($line++, $this->line);
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Rank: " . TextFormat::WHITE . (SessionRank::getInstance()->get($session?->getPlayer()))?->getHighestRank()->getName());
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Online: " . TextFormat::WHITE . count(SessionManager::getInstance()->all()));
      $this->scoreboard?->setLine($line++, "");
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | K: " . TextFormat::WHITE . $session->getKills() . Practice::SERVER_COLOR . "  D: " . TextFormat::WHITE . $session->getDeaths());
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | KDR: " . TextFormat::WHITE . ($session->getKills() === 0 && $session->getDeaths() === 0 ? "0.0" : ($session->getKills() / $session->getDeaths())) . Practice::SERVER_COLOR . "   Wins: " . TextFormat::WHITE . $session->getWins());
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Elo: " . TextFormat::WHITE . $session->getElo());
      $this->scoreboard?->setLine($line++, " ");
      if (($queue = $session->getQueue()) !== null) {
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " Queue: ");
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Ranked: " . ($queue->isRanked() ? TextFormat::GREEN . "YES" : TextFormat::RED . "NO"));
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Kit: " . $queue->getKit());
      } else {
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Fights: " . TextFormat::WHITE . DuelHandler::getInstance()->getDuelsCount());
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Queued: " . TextFormat::WHITE . QueueHandler::getInstance()->getQueueCount());
      }
      $this->scoreboard?->setLine($line++, $this->line . TextFormat::RESET);
      $this->scoreboard?->setLine($line++, Practice::centerText(TextFormat::GRAY . " strommc.ddns.net", 95, true));
    }), 20);
  }

  function setFFA(Session $session): void
  {
    if (($task = TaskManager::getInstance()->get($this->id)) !== null) {
      TaskManager::getInstance()->delete($this->id);
    }
    $line = 0;
    $this->type = self::TYPE_FFA;
    $this->id = TaskManager::getInstance()->set(new ClosureTask(function() use($session, $line): void {
      if (!$session->getPlayer()?->isOnline()) {
        return;
      }
      $this->scoreboard?->spawn();
      $this->scoreboard?->setLine($line++, $this->line);
      $this->scoreboard?->setLine($line++, $this->line . TextFormat::RESET);
      $this->scoreboard?->setLine($line++, Practice::centerText(TextFormat::GRAY . " strommc.ddns.net", 95, true));
    }), 20);
  }

  function setDuel(Session $session): void
  {
    if (($task = TaskManager::getInstance()->get($this->id)) !== null) {
      TaskManager::getInstance()->delete($this->id);
    }
    $line = 0;
    $this->type = self::TYPE_DUEL;
    $this->id = TaskManager::getInstance()->set(new ClosureTask(function() use($session, $line): void {
      if (!$session->getPlayer()?->isOnline()) {
        return;
      }
      $this->scoreboard?->spawn();
      $this->scoreboard?->setLine($line++, $this->line);
      if (($duel = $session->getDuel()) !== null) {
        if ($duel->getPlayer1() === $duel->getPlayer2()) {
          return;
        }
        $opponent = $duel->getPlayer2();
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Fighting with: " . $opponent->getName());
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | Duration: " . $duel->getPlayingTime());
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . "");
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . TextFormat::BOLD . " | " . TextFormat::GREEN . "Your Ping: " . $duel->getPlayer1()->getNetworkSession()->getPing());
        $$this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . TextFormat::BOLD . " | " . TextFormat::GOLD . "Their Ping: " . $opponent->getNetworkSession()->getPing());
      } else {
        $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . " | NO DUEL");
      }
      $this->scoreboard?->setLine($line++, $this->line . TextFormat::RESET);
      $this->scoreboard?->setLine($line++, Practice::centerText(TextFormat::GRAY . " strommc.ddns.net", 95, true));
    }), 20);
  }
}
