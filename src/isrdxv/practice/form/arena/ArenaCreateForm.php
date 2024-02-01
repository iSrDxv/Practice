<?php

namespace isrdxv\practice\form\arena;

use isrdxv\practice\manager\ArenaManager;

use pocketmine\Server;
use pocketmine\player\Player;

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
    $kits = $args[2] ?? [];
    parent::__construct("Arena Menu", [
      new Label("info", "This creates an arena based on the name"),
      new Input("name", "Please provide the name of the arena that you want to create:"),
      new Dropdown("world", "Please provide the name of the arena's world:", $worlds),
      new Dropdown("type", "Please provide the type of the arena:", $types),
      new Dropdown("kit", "Please provide the kit of the arena:", $kits)
    ], function(Player $player, CustomFormResponse $response): void {
      var_dump($response);
      if (isset($worlds, $types, $kits) && isset($worlds[$response->getString("world")], $types[$response->getString("type")], $kits[$response->getString("kit")])) {
        
      }
    });
  }
}