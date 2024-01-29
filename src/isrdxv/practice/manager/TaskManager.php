<?php
declare(strict_types=1);

namespace isrdxv\practice\manager;

use isrdxv\practice\PracticeLoader;

use pocketmine\Server;
use pocketmine\scheduler\{
  Task,
  AsyncTask
};
use pocketmine\utils\SingletonTrait;

class TaskManager
{
  use SingletonTrait;
  
  private $loader;
  
  private array $tasks = [];
  
  function __construct(PracticeLoader $loader)
  {
    self::setInstance($loader);
    $this->loader = $loader;
  }
  
  function set(Task $task, int $tick = 20): string
  {
    $id = uniqid('', true);
    var_dump($id);
    while(isset($this->tasks[$id])){
      $id = uniqid('', true);
    }
    $this->tasks[$id] = $task;
    $this->loader->getScheduler()->scheduleRepeatingTask($task, $tick);
    return $id;
  }
  
  public function setAsync(AsyncTask $task): string
  {
    $id = uniqid('', true);
    while(isset($this->tasks[$id])){
      $id = uniqid('', true);
    }
    $this->tasks[$id] = $task;
    Server::getInstance()->getAsyncPool()->submitTask($task);
    return $id;
  }
  
  public function get(string $id): Task|AsyncTask
  {
    return $this->tasks[$id] ?? null;
  }
  
  public function delete(string $id): void
  {
    if (empty($this->tasks[$id])) {
      return;
    }
    unset($this->tasks[$id]);
  }
  
  public function all(): array
  {
    return $this->tasks;
  }
  
}