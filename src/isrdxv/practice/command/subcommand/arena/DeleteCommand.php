<?php

namespace isrdxv\practice\command\subcommand\arena;

use isrdxv\practice\PracticeLoader;
use isrdxv\practice\manager\KitManager;
use isrdxv\practice\form\arena\ArenaDeleteForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseSubCommand;
use isrdxv\practice\manager\ArenaManager;

final class DeleteCommand extends BaseSubCommand
{
  
  function __construct()
  {
    parent::__construct("delete", "Eliminate the sand that you like the most", ["d"]);
    $this->setPermission("practice.command.arena");
  }
  
  protected function prepare(): void
  {}
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if (!$sender instanceof Player && $this->testPermissionSilent($sender)) {
      return;
    }
    $arenas = [];
    foreach(ArenaManager::getInstance()->all() as $world) {
      $arenas[] = $world->getName();
    }
    $sender->sendForm(new ArenaDeleteForm($arenas));
  }
  
}