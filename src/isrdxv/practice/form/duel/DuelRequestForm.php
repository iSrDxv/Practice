<?php

namespace isrdxv\practice\form\duel;

use isrdxv\practice\Practice;
use isrdxv\practice\manager\SessionManager;

use dktapps\pmforms\{
  CustomForm,
  CustomFormResponse
};
use dktapps\pmforms\element\{
  Label,
  Dropdown
};

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

use DateTime;
use DateTimeZone;

final class DuelRequestForm extends CustomForm
{
  
  function __construct(...$args)
  {
    $to = $args[0] ?? null;
    $kits = $args[1] ?? [];
    $buttons = [];
    if (empty($to)) {
      if (count(SessionManager::getInstance()->all()) > 0) {
        $buttons[] = new Dropdown("request", "Request to: ", array_keys(SessionManager::getInstance()->all()));
        if (is_array($kits) && $kits !== []) {
          $buttons[] = new Dropdown("kit", "Select a Kit: ", $kits);
        } else {
          $buttons[]= new Label("kit_available", "No kits available");
        }
      } else {
        $buttons[] = new Label("online", TextFormat::RED . "There are no players online for your match");
      }
    } else {
      $buttons[] = new Label("online", TextFormat::GREEN . "Request to: " . $to?->getDisplayName());
      if (is_array($kits) && $kits !== []) {
        $buttons[] = new Dropdown("kit", "Select a Kit: ", $kits);
      } else {
        $buttons[]= new Label("kit_available", "No kits available");
      }
    }
    parent::__construct("Duel Request", $buttons, function(Player $player, CustomFormResponse $response): void {
      $player->sendMessage(print_r($response, true));
    }, function(Player $player): void {
        $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "Thank you for inviting a player to a match");
    });
  }
  
}