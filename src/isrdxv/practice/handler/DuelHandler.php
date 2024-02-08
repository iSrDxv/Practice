<?php
declare(strict_types=1);

namespace isrdxv\practice\handler;

use isrdxv\practice\{
    Practice,
    PracticeLoader
};

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

final class DuelHandler
{
    use SingletonTrait;

    function __construct()
    {
        self::setInstance($this);
    }

    function putInDuel(Player $player, Player $opponent, string $kit, bool $ranked): void
    {

    }
}