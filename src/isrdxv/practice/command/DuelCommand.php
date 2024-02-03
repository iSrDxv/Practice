<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\KitManager;
use isrdxv\practice\form\duel\DuelRequestForm;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class DuelCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "duel", TextFormat::DARK_AQUA . "Send pvp request");
      $this->setAliases(["d"/*, "1vs1"*/]);
      $this->setUsage("/duel <player>");
      $this->setPermission("practice.command.duel");
  }

  protected function prepare(): void
  {
    $this->registerArgument(0, new RawStringArgument("name"));
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if ($sender instanceof Player) {
      if (isset($args["name"])) {
        $player = Server::getInstance()->getPlayerExact($args["name"]);
        $kits = array_keys(KitManager::getInstance()->all());
        if ($player->getName() === $sender->getName()) {
          $sender->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "You cannot send the request to yourself");
          return;
        }
        $sender->sendForm(new DuelRequestForm($player, $kits, []));
        return;
      }
    } else {
      $this->sendUsage();
    }
  }
}