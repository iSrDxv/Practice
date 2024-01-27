<?php

namespace isrdxv\practice\command;

use isrdxv\practice\PracticeLoader;
use isrdxv\practice\command\subcommand\arena\{
  HelpCommand
};

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

class ArenaCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "arena", TextFormat::DARK_AQUA . "Command for the sands");
      //$this->setAliases(["a"]);
      $this->setPermission("practice.command.arena");
  }

  protected function prepare(): void
  {
    //these are its subcommands
    $this->registerSubCommand(new HelpCommand());
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