<?php

namespace isrdxv\practice\command;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

class KickCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "kick", TextFormat::DARK_AQUA . "kick the selected player ");
      $this->setAliases(["k"]);
      $this->setUsage("/kick <player>");
      $this->setPermission("practice.command.hub");
  }
  protected function prepare(): void{
    
  }
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
    
   }
}