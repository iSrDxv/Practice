<?php
declare(strict_types=1);

namespace isrdxv\practice\form\ffa;

use dktapps\pmforms\{
    MenuForm,
    MenuOption,
    FormIcon
};
use isrdxv\practice\manager\ArenaManager;
use isrdxv\practice\manager\SessionManager;
use isrdxv\practice\Practice;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class FFAForm extends MenuForm
{

    function __construct(...$args)
    {
        $arenas = $args[0];
        $options = [];
        foreach($arenas as $name) {
            if (($arena = ArenaManager::getInstance()->get($name)) !== null) {
                $options[] = new MenuOption(Practice::SERVER_COLOR . $arena->getName() . TextFormat::EOL . Practice::SERVER_COLOR . "Players: " . count($arena->getPlayers()), new FormIcon($arena->getIcon(), FormIcon::IMAGE_TYPE_PATH));
            }
        }
        parent::__construct("FFA Menu", "Choose the sand that you like or want the most", $options, function(Player $player, int $selectedOption) use($arenas): void {
            if (isset($arenas[$selectedOption])) {
                ($arenas[$selectedOption])->addPlayer($player);
            }
        });
    }
}