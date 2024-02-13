<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\command\subcommand\kit\{
  CreateCommand
};

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

class KitCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "kit", Practice::SERVER_COLOR . "Commands for the kits");
      $this->setPermission("practice.command.kit");
      //$this->setAliases(["a"]);
  }
  
  protected function prepare(): void
  {
    //these are its subcommands
    //$this->registerSubCommand(new HelpCommand());
    $this->registerSubCommand(new CreateCommand());
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    $help = [
      TextFormat::BOLD . TextFormat::YELLOW . "KitManager" . TextFormat::RESET,
      TextFormat::RED . "No subcommand provided, try using /" . $aliasUsed . " help"
    ];
    $sender->sendMessage(implode("\n", $help));
  }
  
}