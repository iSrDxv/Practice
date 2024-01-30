<?php

namespace isrdxv\practice\session\misc;

use isrdxv\practice\Practice;
use isrdxv\practice\session\Session;
use isrdxv\scoreboard\ScoreboardLib;
use isrdxv\practice\manager\{
  SessionManager,
  TaskManager
};

use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

class ScoreboardHandler
{
  const TYPE_LOBBY = "lobby";
  const TYPE_DUEL = "duel";
  const TYPE_FFA = "ffa";
  const TYPE_PARTY = "party";
  
  private Player $player;
  
  private ?string $type;
 
 private string $id;
 
  private string $line;
  
  private ?ScoreboardLib $scoreboard;
  
  function __construct(Player $player)
  {
    $this->player = $player;
    $this->type = null;
    $this->line = TextFormat::DARK_GRAY . " ----------------";
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
        $this->type = null;
        $this->id = "";
      }
      $this->scoreboard = $this->scoreboard ?? ScoreboardLib::create($this->player, TextFormat::BOLD . Practice::SERVER_NAME);
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
      $task->getHandler()?->cancel();
      TaskManager::getInstance()->delete($this->id);
    }
    $line = 0;
    $this->type = self::TYPE_LOBBY;
    $this->id = TaskManager::getInstance()->set(new ClosureTask(function() use($session, $line): void {
      $this->scoreboard?->setLine($line++, Practice::centerLine($this->line, Practice::getPixelLength($this->line)));
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . "| Online: " . TextFormat::WHITE . count(SessionManager::getInstance()->all()));
      $this->scoreboard?->setLine($line++, "");
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . "| Kills: " . TextFormat::WHITE . $session->getKills());
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . "| Deaths: " . TextFormat::WHITE . $session->getDeaths());
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . "| KDR: " . TextFormat::WHITE . ($session->getKills() / $session->getDeaths()));
      $this->scoreboard?->setLine($line++, Practice::SERVER_COLOR . "| Elo: " . TextFormat::WHITE . $session->getElo());
      $this->scoreboard?->setLine($line++, " ");
      //queue
      $this->scoreboard?->setLine($line++, Practice::centerLine($this->line . TextFormat::RESET, Practice::getPixelLength($this->line)));
      $this->scoreboard?->setLine($line++, Practice::centerText(TextFormat::GRAY . " test", 95, true));
    }), 20);
  }
}