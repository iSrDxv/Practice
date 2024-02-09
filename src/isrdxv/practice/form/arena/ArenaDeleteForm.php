<?php

namespace isrdxv\practice\form\arena;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Label;
use isrdxv\practice\manager\ArenaManager;
use isrdxv\practice\Practice;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ArenaDeleteForm extends CustomForm
{

    function __construct(...$args)
    {
        $arenas = $args[0] ?? ["no available"];
        parent::__construct("Arena Menu", [
            new Label("desc", "Select the sand you want to remove"),
            new Dropdown("arena", "Select the Arena: ", $arenas)
        ], function(Player $player, CustomFormResponse $response): void {
            $arena = $response->getString("arena") ?? null;
            if (empty($arena)) {
                $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "This arena is not available");
                return;
            }
            if (ArenaManager::getInstance()->delete($arena)) {
                $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "Arena was removed correctly");
            } else {
                $player->sendMessage(Practice::SERVER_PREFIX . TextFormat::RED . "Could not remove Arena");
            }
        });
    }
}