<?php

namespace isrdxv\practice\task;

use isrdxv\practice\Practice;
use isrdxv\practice\manager\SessionManager;

use pocketmine\Server;
use pocketmine\scheduler\Task;

class BroadcastTask extends Task
{
	function onRun(): void
	{
		$message = /*Practice::SERVER_PREFIX .*/ Practice::BROADCAST_LIST;
		var_dump($message);
		/*foreach(SessionManager::getInstance()->all() as $session) {
			$session->getPlayer()?->sendMessage($message);
		}*/
	}
}