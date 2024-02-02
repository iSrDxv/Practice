<?php

namespace isrdxv\practice\command;

use isrdxv\practice\PracticeLoader;
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
      parent::__construct($loader, "kit", TextFormat::DARK_AQUA . "Commands for the kits");
      //$this->setAliases(["a"]);
      $this->setPermission("practice.command.kit");
  }

  protected function prepare(): void
  {
    //these are its subcommands
    //$this->registerSubCommand(new HelpCommand());
    //$this->registerSubCommand(new CreateCommand());
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