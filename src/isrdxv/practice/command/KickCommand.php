<?php

namespace isrdxv\practice\command;

use isrdxv\practice\Practice;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class KickCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "kick", TextFormat::DARK_AQUA . "kick the selected player");
      $this->setAliases(["k"]);
      $this->setUsage("/kick <player> <reason>");
      $this->setPermission("practice.command.kick");
  }
  
  protected function prepare(): void
  {
    $this->registerArgument(0, new RawStringArgument("name", true));
    $this->registerArgument(0, new RawStringArgument("reason", true));
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if(count($args["name"]) === 0){
			$this->sendUsage();
			return;
		}
		$name = $args["name"] ?? "";
		$reason = $args["reason"] ?? null;
		$player = Server::getInstance()->getPlayerExact($name);
		if(!$player instanceof Player){
			$sender->sendMessage("§cThe player you are looking for is not connected!");
   		 return;
		}
		if(empty($reason)){
			$reason = "Kicked by Staff";
		}
		$player->close("§cYou were kicked from the network for the reason§f: §e$reason");
		Server::getInstance()->broadcastMessage("§cThe player §f$name was kicked by the staff due to§f: §e$reason");
	}
} 