<?php
declare(strict_types=1);

namespace isrdxv\practice\session\misc;

use isrdxv\practice\PracticeLoader;

class StaffData
{
  private int $bans;
  
  private int $kicks;
  
  private int $mutes;
  
  private int $reports;
  
  function __construct(array $data)
  {
    $this->bans = $data["bans"] ?? 0;
    $this->kicks = $data["kicks"] ?? 0;
    $this->mutes = $data["mutes"] ?? 0;
    $this->reports = $data["reports"] ?? 0;
  }
  
  function getBans(): int
  {
    return $this->bans;
  }
  
  function getKicks(): int
  {
    return $this->kicks;
  }
  
  function getMutes(): int
  {
    return $this->mutes;
  }
  
  function getReports(): int
  {
    return $this->reports;
  }
  
  function addBan(): void
  {
    $this->bans++;
  }
  
  function addKick(): void
  {
    $this->kicks++;
  }
  
  function addMute(): void
  {
    $this->mutes++;
  }
  
  function addReport(): void
  {
    $this->reports++;
  }
  
  function subtractBan(): void
  {
    $this->bans--;
  }
  
  function subtractKick(): void
  {
    $this->kicks--;
  }
  
  function subtractMute(): void
  {
    $this->mutes--;
  }
  
  function subtractReport(): void
  {
    $this->reports--;
  }
  
  /**
    * NOTE: save the data of this class in a Database
    */
  function save($database, string $xuid, string $name): void
  {
    $database->executeInsert("practice.staff.stats", ["xuid" => $xuid, "name" => $name, "bans" => $this->bans, "kicks" => $this->kicks, "mutes" => $this->mutes, "reports" => $this->reports]);
  }
  
}