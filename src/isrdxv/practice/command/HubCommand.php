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
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;

class HubCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "hub", TextFormat::DARK_AQUA . "Back to hub");
      $this->setAliases(["lobby", "spawn"]);
      $this->setPermission("practice.command.hub");
  }

  protected function prepare(): void
  {}
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if ($sender instanceof Player && ($session = SessionManager::getInstance()->get($sender)) !== null) {
      $sender->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "You have been teleported to the hub");
      $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
      $defaultWorld->loadChunk($defaultWorld->getSpawnLocation()->getX(), $defaultWorld->getSpawnLocation()->getZ());
      $sender->teleport($defaultWorld->getSpawnLocation());
      ItemManager::spawnLobbyItems($sender);
    }
  }
}