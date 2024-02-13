<?php

namespace isrdxv\practice\listener;

use isrdxv\practice\arena\Arena;
use isrdxv\practice\handler\DuelHandler;
use isrdxv\practice\manager\ArenaManager;
use isrdxv\practice\session\Session;
use isrdxv\practice\manager\SessionManager;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerQuitEvent;

class DuelListener implements Listener
{

    /**
     * PRIORITY: Only FFA Mode
     */
    function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $session = SessionManager::getInstance()->get($player);
        if ($session !== null && ($kit = $session->getKit()) !== null) {
            if ($kit->getDataInfo()->type === Arena::TYPE_FFA) {
                foreach(ArenaManager::getInstance()->getAllNoDuel() as $ffa) {
                    if ($ffa->isPlayer($player)) {
                        $ffa->removePlayer($player->getName());
                    }
                }
            }
        }
    }

    function onExhaust(PlayerExhaustEvent $event): void
    {
        $event->cancel();
    }

    function onCraft(CraftItemEvent $event): void
    {
        $event->cancel();
    }
}