<?php
declare(strict_types=1);

namespace isrdxv\practice\duel;

use isrdxv\practice\arena\type\DuelArena;
use isrdxv\practice\duel\world\DuelWorld;
use isrdxv\practice\kit\DefaultKit;
use isrdxv\practice\manager\ItemManager;
use isrdxv\practice\manager\SessionManager;
use isrdxv\practice\Practice;
use isrdxv\practice\session\Session;
use isrdxv\practice\utils\Time;
use isrdxv\practice\handler\DuelHandler;

use pocketmine\player\GameMode;
use pocketmine\Server;
use pocketmine\player\Player;

use exodus\worldbackup\WorldBackup;

use pocketmine\utils\TextFormat;
use pocketmine\world\World;

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

    private Session $session1;

    private Session $session2;

    private DefaultKit $kit;

    private bool $ranked;

    private int $phase;
    
    private int $timeStart;

    private int $timePlay;

    private int $timeFinish;
    
    private array $spectators = [];

    /** 
     * NOTE: ya tu sabes mamadas necesarias
     */
    function __construct(string $id, DuelArena $arena, DuelWorld $world, Player $p1, Player $p2, DefaultKit $kit, bool $ranked)
    {       
        $this->id = $id;
        $this->arena = $arena;
        $this->world = $world;
        Server::getInstance()->getWorldManager()->unloadWorld($arena?->getWorld(), true);
        $this->p1 = $p1;
        $this->p2 = $p2;
        $this->session1 = SessionManager::getInstance()->get($p1);
        $this->session2 = SessionManager::getInstance()->get($p2);
        $this->kit = $kit;
        $this->ranked = $ranked;
        /**
         * TODO: #4 esto puede cambiar a futuro o ser eliminado
         */
        $this->timeStart = 20;
        $this->timePlay = Time::minutesToTicks(5);
        $this->timeFinish = 10;
        $this->prepare();
    }
    
    function getKit(): DefaultKit
    {
      return $this->kit;
    }
    
    function isRanked(): bool
    {
      return $this->ranked;
    }
    
    function isSpectator(Session $session): bool
    {
        return isset($this->spectators[spl_object_id($session)]);
    }
    function update(): void
    {
        switch($this->phase) {
            case self::PHASE_STARTING:
                $start = $this->timeStart;
                if ($start <= 0) {
                    $this->start();
                    return;
                }
                $message = Practice::SERVER_COLOR . "Starting in " . $this->timeStart . "...";
                $this->p1?->sendMessage($message);
                $this->p2?->sendMessage($message);

                $start--;
            break;
            case self::PHASE_PLAYING:
                $play = $this->timePlay;   
                switch($play % 8000 === 5) {
                    case 5: case 4: case 3: case 2: case 1:
                        $message = Practice::SERVER_COLOR . "Ending game in " . $play . "....";
                        $this->p1?->sendMessage($message);
                        $this->p2?->sendMessage($message);
                    break;
                    case 0:
                        $this->stop(null, true);
                    break;
                }
                $play--;
            case self::PHASE_FINISHED:
            $this->timeFinish--;
        }
    }

    protected function prepare(): void
    {      
        $this->phase = self::PHASE_STARTING;

        $session1 = $this->session1;
        $session2 = $this->session2;

        $player1 = $this->p1;
        $player2 = $this->p2;

        if (!$player1->isOnline() or !$player2->isOnline()) {
            $this->stop();
            return;
        }
        $world = $this->world?->getCopyWorld();
        $world->setTime(World::TIME_DAY);
        $world->stopTime();

        if ($this->ranked) {
            $msg1 = [
                Practice::SERVER_COLOR . "Kit: " . $this->kit?->getName() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Your elo: " . $session1?->getElo() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Opponent: " . $player2->getDisplayName() . " " . $session2->getElo() . TextFormat::EOL,
            ];
            $msg2 = [
                Practice::SERVER_COLOR . "Kit: " . $this->kit?->getName() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Your elo: " . $session2?->getElo() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Opponent: " . $player1->getDisplayName() . " " . $session1->getElo() . TextFormat::EOL,
            ];            
            $player1->sendMessage(implode("\n", $msg1));
            $player2->sendMessage(implode("\n", $msg2));
        } else {
            $msg1 = [
                Practice::SERVER_COLOR . "Kit: " . $this->kit?->getName() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Your elo: " . $session1?->getElo() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Opponent: " . $player2->getDisplayName() . TextFormat::EOL,                
            ];
            $msg2 = [
                Practice::SERVER_COLOR . "Kit: " . $this->kit?->getName() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Your elo: " . $session1?->getElo() . TextFormat::EOL,
                Practice::SERVER_COLOR . "Opponent: " . $player2->getDisplayName() . TextFormat::EOL,                
            ];
            $player1->sendMessage(implode("\n", $msg1));
            $player2->sendMessage(implode("\n", $msg2));
        }
        $session1?->clear();
        $session2?->clear();
        $player1->setGamemode(GameMode::SURVIVAL());
        $player2->setGamemode(GameMode::SURVIVAL());

        $player1->teleport($this->arena->getSpawn1());
        $player2->teleport($this->arena->getSpawn2());

        $this->giveKit($player1);
        $this->giveKit($player2);

        $player1->showPlayer($player2);
        $player2->showPlayer($player1);
    }

    protected function giveKit(Player $player): void
    {
        if (!$player->isOnline()) {
            return;
        }
        $this->kit?->giveTo($player);
    }

    function start(): void
    {
        $player1 = $this->p1;
        $player2 = $this->p2;
        if (!$player1->isOnline() or !$player2->isOnline()) {
            $this->stop();
            return;
        }
        $player1?->sendMessage(Practice::SERVER_COLOR . "Match started!!");
        $player2?->sendMessage(Practice::SERVER_COLOR . "Match started!!");

        $player1?->sendTitle("Fight!", "good luck");
        $player2?->sendTitle("Fight!", "good luck");
        $this->phase = self::PHASE_PLAYING;
    }

    function stop(Session $winner = null, bool $stopped = false): void
    {
        $session1 = $this->session1;
        $session2 = $this->session2;

        $this->phase = self::PHASE_RESTARTING;

        if ($winner !== null) {
        }
        $session1?->clear();
        $session2?->clear();

        if ($stopped) {
            $this->restart();
        }
    }

    private function restart(): void
    {
        $world = $this->world?->getCopyWorld();
        Server::getInstance()->getWorldManager()->unloadWorld($world, true);

        $directory = Server::getInstance()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $this->world?->getIdCopy();
        WorldBackup::deleteBackup($directory);

        DuelHandler::getInstance()->remove($this->world?->getIdCopy());
    }

}