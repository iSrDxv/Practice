<?php

namespace isrdxv\practice\form\user\profile;

use dktapps\pmforms\{
  MenuForm,
  MenuOption,
  FormIcon
};

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ProfileMenuForm extends MenuForm
{
  
  function __construct()
  {
    parent::__construct("Profile", "Your settings and configurations for your gameplay on the server are here", [
      new MenuOption("Game", new FormIcon("textures/ui/controller_glyph_color", FormIcon::IMAGE_TYPE_PATH)),
      new MenuOption("Settings", new FormIcon("textures/ui/automation_glyph_color", FormIcon::IMAGE_TYPE_PATH)),
      new MenuOption(TextFormat::RED . "coming soon...", new FormIcon("textures/ui/Caution", FormIcon::IMAGE_TYPE_PATH))
      ], function(Player $player, int $selectedOption): void {
        switch($selectedOption){
          case 0:
            //$player->sendForm(new ProfileGameForm());
          break;
          case 1:
            //$player->sendForm(new ProfileSettingsForm());
          break;
          case 2:
            //$player->sendForm(new SoonForm());
          break;
        }
      });
  }
  
}