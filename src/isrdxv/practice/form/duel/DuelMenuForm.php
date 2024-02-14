<?php

namespace isrdxv\practice\form\duel;

use isrdxv\practice\Practice;
use isrdxv\practice\form\duel\DuelModeForm;
use isrdxv\practice\manager\{
  KitManager,
  SessionManager
};

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
    parent::__construct(TextFormat::AQUA . "Duel Menu", "Welcome " . $args[0]["name"] . ", what game do you want to play today?", [
      new MenuOption("Ranked Duel", new FormIcon("textures/ui/filledStar", FormIcon::IMAGE_TYPE_PATH)),
      new MenuOption("UnRanked Duel", new FormIcon("textures/ui/filledStarFocus", FormIcon::IMAGE_TYPE_PATH)),
      new MenuOption("Duel Request", new FormIcon("textures/ui/Feedback", FormIcon::IMAGE_TYPE_PATH)),
      new MenuOption("Duel History", new FormIcon("textures/ui/fire_resistance_effect", FormIcon::IMAGE_TYPE_PATH))
      ], function(Player $player, int $selectedOption): void {
        switch($selectedOption){
          case 0:
            $player->sendForm(new DuelModeForm(true));
          break;
          case 1:
            $player->sendForm(new DuelModeForm());
          break;
          case 2:
            $onlinePlayers = array_keys(SessionManager::getInstance()->all());
            $kits = array_keys(KitManager::getInstance()->all());
            $player->sendForm(new DuelRequestForm($player, $kits, $onlinePlayers));
          break;
        }
      }, function(Player $player): void {
        $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Thanks for game a match");
      });
  }
}