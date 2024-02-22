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
		$key = 0;
		$message = implode("\n", Practice::BROADCAST_LIST[$key]);
		var_dump($message);
		foreach(SessionManager::getInstance()->all() as $session) {
			$session->getPlayer()?->sendMessage(Practice::SERVER_PREFIX . $message);
		}
		$key++;
	}
}