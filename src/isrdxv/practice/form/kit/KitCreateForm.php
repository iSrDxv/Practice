<?php

namespace isrdxv\practice\form\kit;

use isrdxv\practice\Practice;
use isrdxv\practice\kit\DefaultKit;
use isrdxv\practice\kit\misc\{
  KitDataInfo,
  KnockbackInfo,
  EffectsData
};
use isrdxv\practice\manager\KitManager;

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
use isrdxv\practice\arena\misc\ArenaMode;

use function trim;
use function str_contains;

class KitCreateForm extends CustomForm
{
  
  function __construct(...$args)
  {
    parent::__construct("Kit Menu", [
      new Label("info", TextFormat::GOLD . "Create the kit with what you have in inventory"),
      new Input("name", "The name of the kit to create:"),
    ], function(Player $player, CustomFormResponse $response): void {
      $name = $response->getString("name");
      if (isset($name)) {
        $kit = trim(TextFormat::clean($name));
        if (str_contains($kit, " ")) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Kit name contains spaces");
          return;
        }
        if (!in_array($kit, ArenaMode::MODES_NORMALS, true) && !in_array($kit, ArenaMode::MODES_ADVANCED, true)) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Write a name that identifies the data that is loaded in it, examples: " . implode(",", ArenaMode::MODES_NORMALS) . " or " . implode(",", ArenaMode::MODES_ADVANCED));
          return;
        }
        
        $defaultKit = new DefaultKit($kit, $player->getInventory()->getContents(), $player->getArmorInventory()->getContents(), new KitDataInfo(), new KnockbackInfo(), new EffectsData());
        if (KitManager::getInstance()->add($defaultKit)) {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "The kit has been created successfully, enjoy.");
        } else {
          $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "The kit named {$kit} already exists in our data");
        }
      }
    });
  }
}