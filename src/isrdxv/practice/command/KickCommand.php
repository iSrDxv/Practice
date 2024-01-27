<?php

namespace isrdxv\practice\command;

use isrdxv\practice\Practice;

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
    
    if(count($args) < 1){
			$sender->sendMessage("§cUse: /kick <playerName> reason");

			return;
		}
		$name = array_shift($args);
		$reason = "";
		for($i = 0; $i < count($args); $i++){
			$reason .= $args[$i];
			$reason .= " ";
		}
		$reason = substr($reason, 0, strlen($reason) - 1);
		$player = Practice::getInstance()->getServer()->getPlayerByPrefix($name);
		if(!$player instanceof Player){
			$sender->sendMessage("§cThe player you are looking for is not connected!");
   		 return;
		}
		if(empty($reason)){
			$reason = "Kicked by Staff";
		}
		$player->close("§cYou were kicked from the network for the reason§f: §e$reason");
		Practice::getInstance()->getServer()->broadcastMessage("§cThe player §f$name was kicked by the staff due to§f: §e$reason");
	}
} 