<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\SessionManager;
use isrdxv\practice\form\staff\punish\BanForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

use DateTime;
use DateTimeZone;

class BanCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "ban", TextFormat::DARK_AQUA . "Punish a server user");
      $this->setAliases(["b"]);
      $this->setUsage("/ban <player>");
      $this->setPermission("practice.command.ban");
  }

  protected function prepare(): void
  {
    $this->registerArgument(0, new RawStringArgument("name", true));
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if ($sender instanceof Player && array_key_exists("name", $args) && $args["name"] !== "null") {
      $sender->sendForm(new BanForm(["name" => $args["name"]]));
      return;
    } else {
      $sender->sendForm(new BanForm(["name" => ""]));
      return;
    }
    $this->sendUsage();
  }
}