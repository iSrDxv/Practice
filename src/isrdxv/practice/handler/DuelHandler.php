<?php
declare(strict_types=1);

namespace isrdxv\practice\handler;

use isrdxv\practice\{
    Practice,
    PracticeLoader
};
use isrdxv\practice\duel\UserDuel;
use isrdxv\practice\duel\world\DuelWorld;
use isrdxv\practice\manager\ArenaManager;
use isrdxv\practice\manager\ItemManager;
use isrdxv\practice\manager\KitManager;
use isrdxv\practice\manager\SessionManager;
use isrdxv\practice\session\misc\ScoreboardHandler;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

final class DuelHandler
{
    use SingletonTrait;

    /**
     * @var UserDuel[]
     * @iSrDxv
     */
    private array $duels = [];

    function __construct()
    {
        self::setInstance($this);
    }

    function putInDuel(Player $player, Player $opponent, string $kit, bool $ranked): void
    {
        if (($kit = KitManager::getInstance()->get($kit)) === null && ArenaManager::getInstance()->getRandomArena($kit->getName()) === null) {
            ItemManager::spawnLobbyItems($player);
            ItemManager::spawnLobbyItems($opponent);
            $session = SessionManager::getInstance()->get($player);
            $oSession = SessionManager::getInstance()->get($opponent);
            $session?->getScoreboardHandler()?->setScoreboard(ScoreboardHandler::TYPE_LOBBY);
            $oSession?->getScoreboardHandler()?->setScoreboard(ScoreboardHandler::TYPE_LOBBY);
            $message = Practice::SERVER_PREFIX . TextFormat::GRAY . "No arenas available for " . ($ranked ? "Ranked" : "UnRanked") . " " . $kit?->getName();
            $player->sendMessage($message);
            $opponent->sendMessage($message);
            return;
        }
        if (($session = SessionManager::getInstance()->get($player)) !== null && ($oSession = SessionManager::getInstance()->get($opponent)) && $session?->isInLobby() && $oSession?->isInLobby()) {
            QueueHandler::getInstance()->remove($player->getName(), true);
            QueueHandler::getInstance()->remove($opponent->getName(), true);
            $player->getInventory()->clearAll();
            $player->getEffects()->clear();
            $opponent->getInventory()->clearAll();
            $opponent->getEffects()->clear();
            $arena = ArenaManager::getInstance()->getRandomArena($kit->getName());
            $duelWorld = new DuelWorld($arena->getWorld());
            $this->duels[$duelWorld->getIdCopy()] = ($duel = new UserDuel( $duelWorld->getIdCopy(), $arena, $duelWorld, $player, $opponent, $kit, $ranked));
            $session->setDuel($duel);
            $oSession->setDuel($duel);
        }
    }
    
    function remove(string $id): void
    {
      if (!isset($this->duels[$id])) {
        return;
      }
      unset($this->duels[$id]);
    }

    function getPlayersInDuel(string $kit, bool $ranked): int
    {
      $num = 0;
      foreach($this->duels as $duel) {
        if ($kit === $duel->getKit()?->getName() && $ranked === $duel->isRanked()) {
          $num++;
        }
      }
      return $num;
    }
    
    function getDuelsCount(): int
    {
      return count($this->duels);
    }
    
}