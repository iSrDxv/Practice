<?php

namespace isrdxv\practice\listener;

use isrdxv\practice\handler\DuelHandler;
use isrdxv\practice\session\Session;
use isrdxv\practice\manager\SessionManager;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class DuelListener implements Listener
{

    /**
     * 
     */
    function onQuit(PlayerQuitEvent $event): void
    {

    }
}