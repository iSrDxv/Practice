<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\{
  ItemManager,
  SessionManager
};

use pocketmine\Server;
use pocketmine\player\{
  GameMode,
  Player
};
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class DuelCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "hub", TextFormat::DARK_AQUA . "Send pvp request");
      $this->setAliases(["d"/*, "1vs1"*/]);
      $this->setUsage("/duel <player>");
      $this->setPermission("practice.command.duel");
  }

  protected function prepare(): void
  {
    $this->registerArgument(0, new RawStringArgument("name", true));
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if ($sender instanceof Player && ($session = SessionManager::getInstance()->get($sender)) !== null && $this->testPermissionSilent($sender)) {
      if (array_key_exists($args["name"]) && count($args["name"]) === 0) {
        $sender->sendForm();
        return;
      }
      if (($player = Server::getInstance()->getPlayerByPrefix($name = trim(implode(" ", $args)))) !== null && $player->getName() !== $sender->getName()) {
        $sender->sendForm();
        return;
      }
      $sender->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Can not find player $name");
    }
  }
}