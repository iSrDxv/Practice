<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\command\subcommand\arena\{
  HelpCommand,
  CreateCommand
};

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

class ArenaCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "arena", Practice::SERVER_COLOR . "Command for the sands");
      $this->setPermission("practice.command.arena");
      //$this->setAliases(["a"]);
  }

  protected function prepare(): void
  {
    //these are its subcommands
    $this->registerSubCommand(new HelpCommand());
    $this->registerSubCommand(new CreateCommand());
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    $help = [
      TextFormat::BOLD . TextFormat::YELLOW . "ArenaManager" . TextFormat::RESET,
      TextFormat::RED . "No subcommand provided, try using /" . $aliasUsed . " help"
    ];
    $sender->sendMessage(implode("\n", $help));
  }
  
}