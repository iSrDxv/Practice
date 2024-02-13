<?php

namespace isrdxv\practice\command\subcommand\arena;

use isrdxv\practice\PracticeLoader;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\IntegerArgument;

final class HelpCommand extends BaseSubCommand
{
  
  function __construct()
  {
    parent::__construct("help", "View arena commands", ["?"]);
  }
  
  function getPermission(): ?string
  {
    return "practice.command.arena";
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
    $height = $sender instanceof Player ? 8 : 50;
    $commandFragments = array_chunk($available, $height);
    $maxNumber = count($commandFragments);
    if (empty($args["page"]) || $args["page"] <= 0) {
      $pageNumber = 1;
    }elseif ($args["page"] > $maxNumber) {
      $pageNumber = $maxNumber;
    } else {
      $pageMumber = $args["page"];
    }
    $sender->sendMessage(
      TextFormat::BOLD . TextFormat::YELLOW . "ArenaManager" . TextFormat::RESET . TextFormat::GRAY . "(" . $pageNumber . "/" . $maxNumber . ")"
    );
    foreach($commandFragments[$pageNumber - 1] as $subCommand) {
      $sender->sendMessage(TextFormat::YELLOW . "/arena " . $subCommand->getName() . TextFormat::GRAY . " - " . $subCommand->getDescription());
    }
  }
  
}