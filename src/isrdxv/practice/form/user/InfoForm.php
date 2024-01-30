<?php

namespace isrdxv\practice\form\user;

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

final class InfoForm extends MenuForm
{
  
  function __construct(...$args)
  {
    $session = SessionManager::getInstance()->get($args[0]["name"]);
    $information = [
      TextFormat::DARK_AQUA . "Welcome to the information of: " . TextFormat::WHITE . $args[0]["name"],
      TextFormat::GRAY . "Custom Name: " . TextFormat::WHITE . ($session->getCustomName() !== null ? $session->getCustomName() : "null"),
      TextFormat::GRAY . "Rank: " . TextFormat::WHITE . $session->getRank(),
      TextFormat::GRAY . "Language: " . TextFormat::WHITE . $session->getLanguage(),
      TextFormat::GRAY . "Coins: " . TextFormat::WHITE . $session->getCoin(),
      TextFormat::GRAY . "Played for the first time: " . TextFormat::WHITE . $session->getFirstPlayed(),
      TextFormat::GRAY . "Last time played: " . TextFormat::WHITE . $session->getLastPlayed(),
      TextFormat::GRAY . "Kills: " . TextFormat::WHITE . $session->getKills(),
      TextFormat::GRAY . "Wins: " . TextFormat::WHITE . $session->getWins(),
      TextFormat::GRAY . "Deaths: " . TextFormat::WHITE . $session->getDeaths(),
      TextFormat::GRAY . "Device: " . TextFormat::WHITE . $session->getClientData()->getDevice(),
      TextFormat::GRAY . "Controller: " . TextFormat::WHITE . $session->getClientData()->getTouch(),
      TextFormat::GRAY . "Settings: " . TextFormat::WHITE . implode("\n", $session->getSettings()),
    ];
    parent::__construct("Info Menu", implode("\n", $information), [
      new MenuOption("Back")
      ], function(Player $player, int $selectedOption): void {
        switch($selectedOption){
          case 0:
            //code
          break;
        }
      }, function(Player $player): void {
        $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "Thanks for seeing the data");
      });
  }
}