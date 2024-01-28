<?php
declare(strict_types=1);

namespace isrdxv\practice\manager;

use isrdxv\practice\kit\misc\{
  KitDataInfo,
  KnockbackInfo,
  EffectsData
};
use isrdxv\practice\kit\DefaultKit;

use isrdxv\practice\{
  Practice,
  PracticeLoader
};

use pocketmine\utils\SingletonTrait;

use function fclose;
use function json_decode;
use function json_encode;
use function mkdir;
use function scandir;
use function strtolower;
use function unlink;
use function basename;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function fopen;

final class KitManager
{
  use SingletonTrait;
  
	private array $kits = [];
	
	private string $defaultPath;

  function init(): void{
		@mkdir($this->defaultPath = PracticeLoader::getInstance()->getDataFolder() . "kits");
		$kitsData = [];
		$files = scandir($this->defaultPath);
		foreach($files as $file){
			if(str_ends_with($file, ".json") === false){
				continue;
			}
			$kitsData[basename($file, ".json")] = json_decode(file_get_contents($this->defaultPath, $file), true);
		}
	  $this->create($kitsData);
	}

	private function create(array $kitsData): void
	{
		foreach($kitsData as $data){
			if(isset($data["name"], $data["inventory"], $data["armor"], $data["data"], $data["kb"], $data["effects"])){
				$items = [];
				foreach($data["items"] as $slot => $data){
					if(($item = Practice::arrayToItem($data)) !== null){
						$items[$slot] = $item;
					}
				}
				$armors = [];
				foreach($data["armor"] as $slot => $item){
					if(($armor = Practice::arrayToItem($item)) !== null){
						$armors[Practice::convertArmorSlot($slot)] = $armor;
					}
				}
				$this->kits[strtolower($name = $data["name"])] = new DefaultKit($name, $items, $armor, KitDataInfo::decode($data["data"]), KnockbackInfo::decode($data["kb"]), EffectsData::decode($data["effects"]));
			}
		}
	}

  function all(): array
  {
		return $this->kits;
	}

	function add(DefaultKit $kit): bool
	{
		if($this->kits[$localName = strtolower($kit->getName())] !== null){
			return false;
		}
		$this->kits[$localName] = $kit;
		$this->save($kit);
		return true;
	}

  function delete(DefaultKit|string $kit): void
  {
		$name = $kit instanceof DefaultKit ? $kit->getName() : $kit;
		if($this->kits[$localName = strtolower($name)] !== null){
			$kit = $this->kits[$localName];
			@unlink($this->defaultPath . "{$kit->getName()}.json");
			unset($this->kits[$localName]);
		}
	}

  function isKit(string $name): bool
  {
		return $this->get($name) !== null;
	}

	function get(string $name): ?DefaultKit
	{
		if(isset($this->kits[$name])){
			return $this->kits[$name];
		}
		foreach($this->kits as $kit){
			if(strtolower($kit->getName()) === strtolower($name)){
				return $kit;
			}
		}
		return null;
	}

	function save(DefaultKit $kit): void
	{
		if(!file_exists($filePath = $this->defaultPath . "{$kit->getName()}.json")){
			fclose(fopen($filePath, "w"));
		}
		file_put_contents($filePath, json_encode($kit->export()));
	}
	
}