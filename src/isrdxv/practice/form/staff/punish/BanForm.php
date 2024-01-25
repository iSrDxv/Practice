<?php

namespace isrdxv\practice\form\staff\punish;

use isrdxv\practice\Practice;
use isrdxv\practice\manager\SessionManager;

use dktapps\pmforms\{
  CustomForm,
  CustomFormResponse
};
use dktapps\pmforms\element\{
  Label,
  Input,
  Slider
};

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

use DateTime;
use DateTimeZone;

final class BanForm extends CustomForm
{
  
  function __construct(...$args)
  {
    parent::__construct("Ban form", [
      new Input("name", "Enter name: ", "", $args["name"] ?? ""),
      new Input("reason", "Reason:"),
      new Label("text_lol", "Leave everything at 0 to permanently ban"),
      new Slider("day", "Day/s", 0, 30, 1),
      new Slider("hour", "Hour/s", 0, 24, 1),
      new Slider("minute", "Minute/s", 0, 60, 5),
      ], function(Player $player, CustomFormResponse $response): void {
        $name = $response->getString("name") ?? "";
        $reason = $response->getString("reason") ?? "";
        
        if ($name === "") {
          return;
        }elseif ($reason === "") {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "A reason for the ban is necessary");
          return;
        }
        
        $day = $response->getFloat("day");
        $hour = $response->getFloat("hour");
        $minute = $response->getFloat("minute");
        
        $time = null;
        if ($day !== 0 && $hour !== 0 && $minute !== 0) {
          $time = new DateTime("NOW", new DateTimeZone("America/Mexico_City"));
          $time->modify("+$day day");
          $time->modify("+$hour hour");
          $time->modify("+$minute minute");
        }
        $expires = $time === null ? "Forever" : "$day day(s), $hour hour(s), $minute minute(s)";
        $kicked = TextFormat::BOLD . TextFormat::RED . "You have been banned from the network" . TextFormat::EOL . TextFormat::EOL . TextFormat::RESET;
        $kicked .= TextFormat::RED . "Reason: " . TextFormat::DARK_AQUA . $reason . TextFormat::EOL;
        $kicked .= TextFormat::RED . "Duration: " . TextFormat::DARK_AQUA . $expires . TextFormat::EOL;
        $kicked .= TextFormat::GRAY . "Appeal at: " . Practice::SERVER_COLOR . "discord.gg/strommc";
        $staff = $player->getName();
        $cheating = SessionManager::getInstance()->get($name)?->getPlayer();
        if ($cheating instanceof Player) {
          $cheating->kick($kicked);
        }
        $cheaterName = strtolower($cheating->getName()); //para sql
        $duration = ($time === null) ? "-1" : $time->format("Y-m-d H:i");
        //agregar a la tabla de bans de SQL
        $announcement = TextFormat::RED . $staff . " banned " . $cheating->getName() . TextFormat::EOL . "Reason: " . TextFormat::WHITE . $reason;
        foreach(SessionManager::getInstance()->all() as $session) {
          $player = $session->getPlayer();
          $player?->sendMessage($announcement);
        }
        if ($player->isOnline()) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . $cheating->getName() . "banned");
        }
        //agregar puntos por banear a un jugador (para el staff)
      }, function(Player $player): void {
        $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Thanks for banning a cheater");
      });
  }
}