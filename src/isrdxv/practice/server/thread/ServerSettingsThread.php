<?php
declare(strict_types=1);

namespace isrdxv\practice\server\thread;

use dktapps\pmforms\{
  FormIcon,
  ServerSettingsForm
};

use pocketmine\network\mcpe\protocol\ServerSettingsResponsePacket;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;
use pocketmine\Server;

use Threaded;
use ReflectionObject;

use function gc_collect_cycles;
use function gc_enable;
use function gc_mem_caches;
use function igbinary_serialize;
use function igbinary_unserialize;
use function str_replace;

class ServerSettingsThread extends Thread{

	public int $busy_score = 0;
	private SleeperNotifier $notifier;
	private Threaded $actionQueue;
	private Threaded $actionResults;
	private bool $running;
	
	public function __construct(SleeperNotifier $notifier){
		$this->notifier = $notifier;
		$this->actionQueue = new Threaded();
		$this->actionResults = new Threaded();
	}

	public function start(int $options = PTHREADS_INHERIT_ALL) : bool{
		$this->running = true;
		return parent::start($options);
	}

	public function sleep() : void{
		$this->synchronized(function() : void{
			if($this->running){
				$this->wait();
			}
		});
	}

	public function stop() : void{
		$this->running = false;
		$this->synchronized(function() : void{
			$this->notify();
		});
	}

	public function queue() : void{
		$this->synchronized(function() : void{
			$this->actionQueue[] = igbinary_serialize("query");
			++$this->busy_score;
			$this->notifyOne();
		});
	}

	public function triggerGarbageCollector() : void{
		$this->synchronized(function() : void{
			$this->actionQueue[] = igbinary_serialize("garbage_collector");
			$this->notifyOne();
		});
	}

	public function onRun() : void{
		while($this->running){
			while(($queue = $this->actionQueue->shift()) !== null){
				$queue = igbinary_unserialize($queue);
				if($queue === "query"){
					foreach(Server::getInstance()->getOnlinePlayers() as $player) {
					  $form = new ServerSettingsForm("StromMC", [], new FormIcon("textures/ui/title", FormIcon::IMAGE_TYPE_PATH), function(): void {
					    
					  });
					  $reflection = new ReflectionObject($player);
					  $property = $reflection->getProperty("formIdCounter");
					  $property->setAccessible(true);
					  $id = $property->getValue($player);
					  
					  $property->setValue($player, $id++);
					  $id--;
					  
					  $pk = ServerSettingsResponsePacket::create($id, json_encode($form));
					  if ($player->getNetworkSession()->sendDataPacket($pk)) {
					    $formPr = $property->getProperty("form");
					    $formPr->setAccessible(true);
					    
					    $value = $formPr->getValue($player);
					    $value[$id] = $form;
					    $formPr->setValue($player, $value);
					  }
					}
					$this->actionResults[] = igbinary_serialize("packet");
					$this->notifier->wakeupSleeper();
				}elseif($queue === "garbage_collector"){
					gc_enable();
					gc_collect_cycles();
					gc_mem_caches();
				}
			}
			$this->sleep();
		}
	}

	public function collectActionResults() : void{
		while(($result = $this->actionResults->shift()) !== null){
		  var_dump(igbinary_unserialize($result));
			--$this->busy_score;
		}
	}
	
}