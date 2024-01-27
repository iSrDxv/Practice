<?php
declare(strict_types=1);

namespace isrdxv\practice\kit\misc;

use function is_array;

class KitDataInfo
{
  public bool $damagePlayed;
  
  public bool $build;
  
  public string $icon;
  
  function __construct(bool $damagePlayed = false, bool $build = false, string $icon = "")
  {
    $this->damagePlayed = $damagePlayed;
    $this->build = $build;
    $this->icon = $icon;
  }
  
  static function decode($data): self
  {
    if (is_array($data) && isset($data["damage"], $data["build"], $data["icon"])) {
      return new self($data["damage"], $data["build"], $data["icon"]);
    }
    return new self();
  }
  
  function export(): array
  {
    return ["damage" => $this->damagePlayed, "build" => $this->build, "icon" => $this->icon];
  }
  
}