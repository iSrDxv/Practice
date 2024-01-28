<?php

namespace isrdxv\practice\form\arena;

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
    parent::__construct("Arena Menu", [
      new Label("info", "This creates an arena based on the name"),
      new Input("name", "Please provide the name of the arena that you want to create:"),
      new Dropdown("world", "Please provide the name of the arena's world:", $args[0]["worlds"]),
      new Dropdown("type", "Please provide the type of the arena:", $args[1]["types"]),
      new Dropdown("kit", "Please provide the kit of the arena:", $args[2]["kits"])
    ], function(Player $player, CustomFormResponse $response): void {
      
    });
  }
}