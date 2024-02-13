<?php

namespace isrdxv\practice\command\subcommand\kit;

use isrdxv\practice\PracticeLoader;
use isrdxv\practice\form\kit\KitCreateForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseSubCommand;

final class CreateCommand extends BaseSubCommand
{
  
  function __construct()
  {
    parent::__construct("create", "Create the sand that I highlighted the most", ["c"]);
    $this->setPermission("practice.command.kit");
  }
  
  protected function prepare(): void
  {}
  
  function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void
  {
    if (!$sender instanceof Player && $this->testPermissionSilent($sender)) {
      return;
    }
    $sender->sendForm(new KitCreateForm());
  }
  
}