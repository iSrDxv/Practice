<?php

namespace isrdxv\practice\form\duel;

use isrdxv\practice\Practice;
use isrdxv\practice\manager\SessionManager;

use dktapps\pmforms\{
  MenuForm,
  MenuOption,
  FormIcon
};

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

use DateTime;
use DateTimeZone;

final class DuelMenuForm extends MenuForm
{
  
  function __construct(...$args)
  {
    parent::__construct("Match Menu", "Welcome " . $args[0]["name"] . ", what game do you want to play today?", [
      new MenuOption("Ranked Duel"),
      new MenuOption("UnRanked Duel"),
      new MenuOption("Duel Request"),
      new MenuOption("Duel History")
      ], function(Player $player, int $selectedOption): void {
        switch($selectedOption){
          case 0:
            //code
          break;
        }
      }, function(Player $player): void {
        $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Thanks for game a match");
      });
  }
}