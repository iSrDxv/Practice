<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

class MaintenanceCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "maintenance", TextFormat::DARK_AQUA . "Enable or Disable the server under maintenance");
      $this->setPermission("practice.command.maintenance");
  }

  protected function prepare(): void
  {
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    Practice::setMaintenance();
  }
}
