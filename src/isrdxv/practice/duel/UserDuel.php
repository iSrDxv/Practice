<?php
declare(strict_types=1);

namespace isrdxv\practice\duel;

use isrdxv\practice\arena\type\DuelArena;
use isrdxv\practice\duel\world\DuelWorld;
use isrdxv\practice\kit\DefaultKit;
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

    function update(): void
    {
        if ($this->phase === self::PHASE_STARTING) {
            
        }
    }

}