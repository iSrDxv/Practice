<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\{
  ItemManager,
  SessionManager
};

use pocketmine\Server;
use pocketmine\player\{
  GameMode,
  Player
};
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

class ReportCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "report", Practice::SERVER_COLOR . "Report any server problems or unwanted users", ["r", "warning"]);
      $this->setPermission("practice.command.report");
      $this->setUsage("/report");
  }
  
  protected function prepare(): void
  {}
  
  function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void
  {
    if ($sender instanceof Player && ($session = SessionManager::getInstance()->get($sender)) !== null) {
        //$sender->sendForm();
    }
  }
}