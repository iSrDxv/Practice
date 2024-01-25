<?php

namespace isrdxv\practice\command;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};
use isrdxv\practice\manager\SessionManager;

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
      $this->setUsage("Usage: /ban <player> <duration> <reason>");
      $this->setPermission("practice.command.ban");
  }

  protected function prepare(): void
  {
    $this->registerArgument(0, new RawStringArgument("name", true));
    $this->registerArgument(1, new TextArgument("duration", true));
    $this->registerArgument(2, new RawStringArgument("reason", true));
  }
  
  function onRun(CommandSender $sender, string $aliasUsed, array $args): void
  {
    if ($sender instanceof Player && count($args) === 0) {
      $player->sendForm(new BanForm(["name" => ""]));
      return;
    }
    if ($sender instanceof Player && isset($args["name"])) {
      $player->sendForm(new BanForm(["name" => array_shift($args)]));
      return;
    }
    if (count($args) >= 3) {
      $name = $args["name"];
      $duration = $args["duration"];
      $reason = trim(implode(" ", $args["reason"]));
      
      $matches = []; //php, i love you
      if (!preg_match("/^([0-9]+d)?([0-9]+h)?([0-9]+m)?$/", $duration, $matches)) {
        $this->sendUsage();
        return;
      }
      $day = 0;
      $hour = 0;
      $minute = 0;
      foreach($matches as $index => $match) {
        if($index !== 0 && strlen($match) !== 0) {
          var_dump($match);
          $n = substr($match, 0, -1);
          var_dump($n);
				  if(str_ends_with($match, "d")){
				    $day = (int)$n;
					}elseif(str_ends_with($match, "h")) {
						$hour = (int)$n;
					}elseif(str_ends_with($match, "m")) {
						$minute = (int)$n;
					}
        }
      }
      $expires = "Forever";
      $duration = "-1";
      if ($day !== 0 || $hour !== 0 || $minute !== 0) {
        $time = new DateTime("NOW", new DateTimeZone("America/Mexico_Ciy"));
        $time->modify("+$day day");
        $time->modify("+$hour hour");
        $time->modify("+$minute minute");
        $expires = $day . " day(s) " . $hour . " hour(s) " . $minute . " minute(s)";
        $duration = $time->format("Y-m-d H:i");
      }
      $staff = $sender->getName();
      if (($cheating = SessionManager::getInstance()->get($name)) !== null) {
        $name = $cheating->getPlayer()?->getName();
        $kicked = TextFormat::BOLD . TextFormat::RED . "You have been banned from the network" . TextFormat::EOL . TextFormat::EOL . TextFormat::RESET;
        $kicked .= TextFormat::RED . "Reason: " . TextFormat::DARK_AQUA . $reason . TextFormat::EOL;
        $kicked .= TextFormat::RED . "Duration: " . TextFormat::DARK_AQUA . $expires . TextFormat::EOL;
        $kicked .= TextFormat::GRAY . "Appeal at: " . Practice::SERVER_COLOR . "discord.gg/strommc";
        $cheating->getPlayer()?->kick($kicked);
      }
      $announcement = TextFormat::RED . $staff . " banned " . $name . TextFormat::EOL . "Reason: " . TextFormat::WHITE . $reason;
      foreach(SessionManager::getInstance()->all() as $session) {
        $player = $session->getPlayer();
        $player?->sendMessage($announcement);
      }
      if ($sender instanceof Player) {
        if ($sender->isOnline()) {
          //add staff points
          $sender->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . $name . "banned");
        }
      } else {
        $sender->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . $name . "banned");
      }
      //sql
    }
    $this->sendUsage();
  }
}