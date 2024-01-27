<?php

declare(strict_types=1);

namespace isrdxv\practice\kit\misc;

use function is_array;

class KnockbackInfo{

	public float $horizontal;
	public float $vertical;
	public float $maxHeight;
	public bool $canRevert;
	public int $speed;

	public function __construct(float $horizontal = 0.4, float $vertical = 0.4, float $maxHeight = 0.0, bool $canRevert = false, int $speed = 10){
		$this->horizontal = $horizontal;
		$this->vertical = $vertical;
		$this->maxHeight = $maxHeight;
		$this->canRevert = $canRevert;
		$this->speed = $speed;
	}

	public static function decode($data): self{
		if(is_array($data) && isset($data["horizontal"], $data["vertical"], $data["maxHeight"], $data["revert"], $data["speed"])){
			return new self($data["horizontal"], $data["vertical"], $data["maxHeight"], $data["revert"], $data["speed"]);
		}
		return new self();
	}

	public function export() : array{
		return ["horizontal" => $this->horizontal, "vertical" => $this->vertical, "maxHeight" => $this->maxHeight, "revert" => $this->canRevert, "speed" => $this->speed];
	}
	
}