<?php

namespace isrdxv\practice\listener;

use isrdxv\practice\arena\Arena;
use isrdxv\practice\handler\DuelHandler;
use isrdxv\practice\manager\ArenaManager;
use isrdxv\practice\session\Session;
use isrdxv\practice\manager\SessionManager;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{
  PlayerExhaustEvent,
  PlayerQuitEvent,
  PlayerDeathEvent,
    PlayerRespawnEvent
};
use pocketmine\event\player;

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
                    if ($ffa->isHere($player)) {
                        $ffa->removePlayer($player->getName());
                    }
                }
            }
        }
    }
    /**
      * PRIORITY: Only FFA Mode
      */
    function onDeath(PlayerRespawnEvent $event): void
    {
        $player = $event->getPlayer();
        $session = SessionManager::getInstance()->get($player);
        if ($session !== null && ($kit = $session->getKit()) !== null) {
            if ($kit->getDataInfo()->type === Arena::TYPE_FFA) {
                foreach(ArenaManager::getInstance()->getAllNoDuel() as $ffa) {
                    if ($ffa->isHere($player)) {
                        if ($ffa->removePlayer($player->getName())) {
                            $ffa->addPlayer($player);
                        }
                    }
                }
            }
        }
    }

    function onEntity(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();
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