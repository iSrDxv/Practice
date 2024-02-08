<?php
declare(strict_types=1);

namespace isrdxv\practice\duel;

use isrdxv\practice\arena\type\DuelArena;
use isrdxv\practice\duel\world\DuelWorld;
use isrdxv\practice\kit\DefaultKit;
use isrdxv\practice\utils\Time;

use pocketmine\player\Player;

final class UserDuel
{
    const PHASE_STARTING = 0; //empieza la cuenta regresiva (PARA INICIAR LA PARTIDA)

    const PHASE_PLAYING = 1; //empieza el pvp

    const PHASE_FINISHED = 2; //cuando uno de los 2 pierde

    const PHASE_RESTARTING = 3; //reseteo del mapa

    private string $id;

    private DuelArena $arena;

    private DuelWorld $world;

    private Player $p1;

    private Player $p2;

    private DefaultKit $kit;

    private bool $ranked;

    private int $phase = self::PHASE_STARTING;
    
    private array $time = [
      "start" => Time::secondsToTicks(20),
      "play" => Time::minutesToTicks(5)
    ];
    
    function __construct(string $id, DuelArena $arena, DuelWorld $world, Player $p1, Player $p2, DefaultKit $kit, bool $ranked)
    {       
        $this->id = $id;
        $this->arena = $arena;
        $this->world = $world;
        $this->p1 = $p1;
        $this->p2 = $p2;
        $this->kit = $kit;
        $this->ranked = $ranked;
    }
    
    function getKit(): DefaultKit
    {
      return $this->kit;
    }
    
    function isRanked(): bool
    {
      return $this->ranked;
    }
    
    function update(): void
    {
        if ($this->phase === self::PHASE_STARTING) {
          $start = $this->time["start"];
          $start--;
          switch($start % 1000 === 10) {
            case 10: case 9: case 8: case 7:
            case 6: case 5: case 4: case 3:
            case 2: case 1:
              $message = $this->time["start"];
            break;
            case 0:
              return;
            break;
          }
        }elseif($this->phase === self::PHASE_PLAYING) {
          $play = $this->time["play"];
          $play--;
          switch($play % 8000 === 5) {
            case 5: case 4: case 3: case 2: case 1:
            break;
            case 0:
              
            break;
          }
        }
    }

}