<?php
declare(strict_types=1);

namespace isrdxv\practice\kit\misc;

use isrdxv\practice\Practice;

use function count;
use function is_array;

class EffectsData
{
  private array $effects = [];
  
  function __construct(array $effects = [])
  {
    $this->effects = $effects;
  }
  
  static function decode($data): self
  {
    if (is_array($data) && count($data) > 0) {
      $effects = [];
      foreach($data as $effect) {
        if (($effect = Practice::arrayToEffect($effect)) !== null) {
          $effects["d"] = $effect;
        }
      }
      return new self($effects);
    }
    return new self();
  }
  
  function add(): void;
  
  function remove(): void;
  
  function export(): array
  {
    $data = [];
    foreach($this->effects as $effect) {
      $data[] = Practice::effectToArray($effect);
    }
    return $data;
  }
  
}