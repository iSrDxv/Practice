<?php

namespace isrdxv\practice\form\arena;

use dktapps\pmforms\{
    MenuForm,
    MenuOption,
    FormIcon
};

use pocketmine\player\Player;

class ArenaManageForm extends MenuForm
{

    function __construct()
    {
        parent::__construct("Arena Menu", "Choose in which arena you are going to edit your data", [
            new MenuOption("Rename", new FormIcon("textures/ui/UpdateGlyph", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("Change spawns", new FormIcon("textures/ui/UpdateGlyph", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("Change max height", new FormIcon("textures/ui/UpdateGlyph", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("Change view", new FormIcon("textures/ui/UpdateGlyph", FormIcon::IMAGE_TYPE_PATH)),
            new MenuOption("Change world", new FormIcon("textures/ui/UpdateGlyph", FormIcon::IMAGE_TYPE_PATH)),
        ], function(Player $player, int $selectedOption): void {
            switch($selectedOption){
                case 0:
                    //code...
                break;
            }
        });
    }

}