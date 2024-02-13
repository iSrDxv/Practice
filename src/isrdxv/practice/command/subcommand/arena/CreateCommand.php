<?php

namespace isrdxv\practice\command\subcommand\arena;

use isrdxv\practice\PracticeLoader;
use isrdxv\practice\manager\KitManager;
use isrdxv\practice\form\arena\ArenaCreateForm;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseSubCommand;

final class CreateCommand extends BaseSubCommand
{
  
  function __construct()
  {
    parent::__construct("create", "Create the sand that I highlighted the most", ["c"]);
  }
  
  function getPermission(): ?string
  {
    return "practice.command.arena";
  }
  
  protected function prepare(): void
  {}
  
  function onRun(Player|CommandSender $sender, string $aliasUsed, array $args): void
  {
    if (!$sender instanceof Player && $this->testPermissionSilent($sender)) {
      return;
    }
    $worlds = [];
    foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world) {
      $worlds[] = $world->getDisplayName();
    }
    $types = ["Duel", "FFA", "Event", "Bot"];
    $kits = [];
	  foreach(KitManager::getInstance()->all() as $kit){
				$kits[] = $kit->getName();
		}
    $sender->sendForm(new ArenaCreateForm($worlds, $types, $kits));
  }
  
}