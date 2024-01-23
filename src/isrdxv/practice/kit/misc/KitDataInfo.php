<?php
declare(strict_types=1);

namespace isrdxv\practice\kit\misc;

use function is_array;

class KitDataInfo
{
  private bool $damagePlayed;
  
  private bool $build;
  
  private string $texture;
  
  function __construct(bool $damagePlayed = false, bool $build = false, string $texture = "")
  {
    $this->damagePlayed = $damagePlayed;
    $this->build = $build;
    $this->texture = $texture;
  }
  
}