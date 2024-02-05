<?php
declare(strict_types=1);

namespace isrdxv\practice\kit\misc;

use function is_array;

class KitDataInfo
{
  public bool $damagePlayed;
  
  public bool $build;
  
  public bool $enabled;
  
  public string $type;
  
  public string $icon;
  
  function __construct(bool $damagePlayed = false, bool $build = false, bool $enabled = true, string $type = "FFA", string $icon = "")
  {
    $this->damagePlayed = $damagePlayed;
    $this->build = $build;
    $this->enabled = $enabled;
    $this->type = $type;
    $this->icon = $icon;
  }
  
  static function decode($data): self
  {
    if (is_array($data) && isset($data["damage"], $data["build"], $data["enabled"], $data["type"], $data["icon"])) {
      return new self($data["damage"], $data["build"], $data["enabled"], $data["type"], $data["icon"]);
    }
    return new self();
  }
  
  function export(): array
  {
    return ["damage" => $this->damagePlayed, "build" => $this->build, "enabled" => $this->enabled, "type" => $this->type, "icon" => $this->icon];
  }
  
}