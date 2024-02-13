<?php
declare(strict_types=1);

namespace isrdxv\practice\form\duel;

use isrdxv\practice\Practice;

use dktapps\pmforms\{
    MenuForm,
    MenuOption,
    FormIcon
};
use isrdxv\practice\arena\Arena;
use isrdxv\practice\handler\QueueHandler;
use isrdxv\practice\manager\KitManager;
use isrdxv\practice\manager\SessionManager;
use pocketmine\item\Armor;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class DuelModeForm extends MenuForm
{

    function __construct(bool $ranked = false)
    {
        $options = [];
        $names = [];
        $kits = KitManager::getInstance()->allOf(Arena::TYPE_DUEL);
        foreach($kits as $kit) {
            $names[] = strtolower($kit->getName());
            $options[] = new MenuOption(Practice::SERVER_COLOR . $kit->getName() . TextFormat::EOL . QueueHandler::getInstance()->getPlayersOfKit($kit->getName(), $ranked) . " Queuing..." . TextFormat::EOL . TextFormat::GRAY . "Click to Play!", new FormIcon($kit->getDataInfo()->icon, FormIcon::IMAGE_TYPE_PATH));
        }
        parent::__construct(Practice::SERVER_COLOR . ($ranked ? "Ranked" : "UnRanked") . " " . TextFormat::RESET . TextFormat::GRAY . "Duels", TextFormat::GRAY . "Choose the game mode you like the most to defeat your enemies", $options, function(Player $player, int $selectedOption) use($ranked, $names): void {
            if (($session = SessionManager::getInstance()->get($player)) !== null && ($kit = $names[$selectedOption]) !== null) {
                if ($session->getQueue() !== null) {
                    QueueHandler::getInstance()->remove($player->getName(), true);
                    return;
                }
                QueueHandler::getInstance()->add($player, $kit, $ranked);
            }
        });
    }
}