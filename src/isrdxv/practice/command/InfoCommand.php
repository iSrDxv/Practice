<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\SessionManager;
use isrdxv\practice\form\user\InfoForm;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

use DateTime;
use DateTimeZone;

class InfoCommand extends BaseCommand
{
  
  function __construct(PracticeLoader $loader)
  {
      parent::__construct($loader, "info", TextFormat::DARK_AQUA . "View information about a user");
      $this->setAliases(["player"]);
      $this->setUsage("/info <player>");
      $this->setPermission("practice.command.info");
  }

  protected function prepare(): void
  {
    $this->registerArgument(0, new RawStringArgument("name", true));
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if ($sender instanceof Player && array_key_exists("name", $args) && $args["name"] !== "null") {
      $sender->sendForm(new InfoForm(["name" => $args["name"]]));
      return;
    } else {
      $sender->sendForm(new InfoForm(["name" => (string)$sender->getName()]));
      return;
    }
    $this->sendUsage();
  }
}