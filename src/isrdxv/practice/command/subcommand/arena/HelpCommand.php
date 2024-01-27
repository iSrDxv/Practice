<?php

namespace isrdxv\practice\command\subcommand\arena;

use isrdxv\practice\PracticeLoader;

use pocketmine\player\Player;
use pocketmine\command\CommandSender;

use CortexPE\Commando\SubCommand;
use CortexPE\Commando\args\IntegerArgument;

final class HelpCommand extends BaseSubCommand
{
  
  function __construct()
  {
    parent::__construct("help", "View arena commands", ["?"]);
    $this->setPermission("practice.command.arena");
  }
  
  protected function prepare(): void
  {
    $this->registerArgument(0, new IntegerArgument("page", true));
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    $available = [];
    foreach($this->parent->getSubCommands() as $subCommand) {
      $id = spl_object_hash($subCommand);
      if (empty($available[$id]) && $subCommand->testPermissionSilent($sender)) {
        $available[$id] = $subCommand;
      }
    }
    var_dump($available);
  }
  
}