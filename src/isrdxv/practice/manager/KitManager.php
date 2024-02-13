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
use isrdxv\practice\arena\Arena;
use pocketmine\utils\Filesystem;
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
		@mkdir($this->defaultPath = PracticeLoader::getInstance()->getDataFolder() . "kits" . DIRECTORY_SEPARATOR);
		$kitsData = [];
		foreach(glob($this->defaultPath . "*.json") as $file){
			if(!is_file($file) or str_ends_with($file, ".json") === false){
				continue;
			}
			var_dump($file);
			$kitsData[basename($file, ".json")] = json_decode(Filesystem::fileGetContents($file), true, flags: JSON_THROW_ON_ERROR);
		}
		$this->load($kitsData);
	}

	private function load(array $kitsData): void
	{
		foreach($kitsData as $data){
			if(isset($data["name"], $data["inventory"], $data["armor"], $data["data"], $data["kb"], $data["effects"])){
				$items = [];
				foreach($data["inventory"] as $value){
					if($value !== null){
						$items[] = Practice::decodeItem($value);
					}
				}
				$armors = [];
				foreach($data["armor"] as $value){
					if($value !== null){
						$armors[] = Practice::decodeItem($value);
					}
				}
				$this->kits[strtolower($data["name"])] = new DefaultKit($data["name"], $items, $armors, KitDataInfo::decode($data["data"]), KnockbackInfo::decode($data["kb"]), EffectsData::decode($data["effects"]));
			}
		}
	}

    function all(string $type = Arena::TYPE_FFA): array
	{
		$data = [];
		foreach($this->kits as $kit) {
			if ($kit->getDataInfo()->type === Arena::TYPE_DUEL) {
				$data[strtolower($kit->getName())] = $kit;
			}
			if ($kit->getDataInfo()->type === Arena::TYPE_FFA) {
				$data[strtolower($kit->getName())] = $kit;
			}
		}
		return $data;
	}

	function getAll(): array
	{
		return $this->kits;
	}

	function add(DefaultKit $kit): bool
	{
		if(isset($this->kits[$mainName = strtolower($kit->getMainName())])){
			return false;
		}
		$this->kits[$mainName] = $kit;
		$this->save($kit);
		return true;
	}

  function delete(DefaultKit|string $kit): void
  {
		$name = $kit instanceof DefaultKit ? $kit->getMainName() : $kit;
		if($this->kits[$mainName = strtolower($name)] !== null){
			$kit = $this->kits[$mainName];
			@unlink($this->defaultPath . "{$kit->getName()}.json");
			unset($this->kits[$mainName]);
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
			if(strtolower($kit->getMainName()) === strtolower($name)){
				return $kit;
			}
		}
		return null;
	}

	function save(DefaultKit $kit): void
	{
		if(!file_exists($filePath = $this->defaultPath . $kit->getName() . ".json")){
			Filesystem::safeFilePutContents($filePath, json_encode([], JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR));
		}
		Filesystem::safeFilePutContents($filePath, json_encode($kit, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING | JSON_THROW_ON_ERROR));
	}

}