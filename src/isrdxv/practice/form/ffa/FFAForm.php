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
        $classes = [];
        $options = [];
        foreach($args[0] as $name) {
            if (($arena = ArenaManager::getInstance()->get($name)) !== null) {
                $classes[] = $arena;
                $options[] = new MenuOption(Practice::SERVER_COLOR . $arena->getName() . TextFormat::EOL . Practice::SERVER_COLOR . "Players: " . count($arena->getPlayers()), new FormIcon($arena->getIcon(), FormIcon::IMAGE_TYPE_PATH));
            }
        }
        parent::__construct(TextFormat::AQUA . "FFA Menu", "Choose the sand that you like or want the most", $options, function(Player $player, int $selectedOption) use($classes): void {
            if (($arena = $classes[$selectedOption]) !== null) {
                $arena->addPlayer($player);
            }
        });
    }
}