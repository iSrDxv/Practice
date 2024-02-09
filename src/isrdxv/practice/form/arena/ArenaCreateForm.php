<?php

namespace isrdxv\practice\form\arena;

use isrdxv\practice\Practice;
use isrdxv\practice\manager\{
  ArenaManager,
  KitManager
};

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

use dktapps\pmforms\{
  CustomForm,
  CustomFormResponse
};
use dktapps\pmforms\element\{
  Label,
  Input,
  Dropdown
};

class ArenaCreateForm extends CustomForm
{
  
  function __construct(...$args)
  {
    var_dump($args);
    $worlds = $args[0] ?? [];
    $types = $args[1] ?? [];
    $kits = $args[2] ?? ["no available"];
    parent::__construct("Arena Menu", [
      new Label("info", "This creates an arena based on the name"),
      new Input("name", "Please provide the name of the arena that you want to create:"),
      new Dropdown("world", "Please provide the name of the arena's world:", $worlds),
      new Dropdown("type", "Please provide the type of the arena:", $types),
      new Dropdown("kit", "Please provide the kit of the arena:", $kits)
    ], function(Player $player, CustomFormResponse $response): void {
      var_dump($response);
      if (isset($worlds, $types, $kits)) {
        $world = Server::getInstance()->getWorldManager()->getWorldByName($worlds[$response->getInt("world")]);
        if (empty($world)) {
          $player->sendMessage(Practice::SERVER_PREFIX .TextFormat::RED . "There is no world called: " . $worlds[$response->getInt("world")]);
          return;
        }
        $kitName = (string)$kits[$response->getInt("kit")];
        $kit = KitManager::getInstance()->get($kitName);
        if (empty($kit)) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . $kits[$response->getInt("kit")] . " does not exist");
          return;
        }
        
        $type = $types[$response->getInt("type")];
        if (!in_array($type, $types, true)) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "There is no such type of arena");
          return;
        }
        if ($kit->getDataInfo()->type === $type && !$kit->getDataInfo()->enabled) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Kit disabled!!");
          return;
        }
        
        $name = TextFormat::clean($response->getString("name"));
        if (str_contains($name, " ")) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "The name cannot contain spaces");
          return;
        }
        
        $arena = ArenaManager::getInstance()->get($name);
        if ($arena !== null) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "The arena already exists");
          return;
        }
        if (ArenaManager::getInstance()->create($name, $type, $world, $kit)) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "The arena has been created successfully");
        } else {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "The arena could not be created, we are very sorry :c");
        }
      }
    });
  }
}