<?php
declare(strict_types=1);

namespace isrdxv\practice\handler;

use isrdxv\practice\{
    Practice,
    PracticeLoader
};
use isrdxv\practice\duel\invite\DuelInvite;
use isrdxv\practice\duel\UserDuel;
use isrdxv\practice\duel\world\DuelWorld;
use isrdxv\practice\manager\{
  SessionManager,
  ItemManager,
  ArenaManager,
  KitManager
};
use isrdxv\practice\session\misc\ScoreboardHandler;

use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

final class DuelInviteHandler
{
    use SingletonTrait;

    /**
     * @var UserDuel[]
     * @iSrDxv
     */
    private array $invites = [];

    function __construct()
    {
        self::setInstance($this);
    }

    function get(string $name): ?DuelInvite
    {
        return $this->invites[$name] ?? null;
    }

    function all(): array
    {
        return $this->invites;
    }

    function sendRequest(Player $to, Player $from, string $kit, bool $ranked = false): void
    {
        $request = $this->invites;
        if ($from->isOnline()) {
            $from->sendMessage(Practice::SERVER_PREFIX . TextFormat::GREEN . "Fight request to " . Practice::SERVER_COLOR . $to->getDisplayName() . " successfully ");
        }
    }

    function acceptRequest(): void
    {

    }

    function remove(string $name, bool $message = false): void
    {
        if (($invite = $this->get($name))) {
            unset($this->invites[$name]);
            if ($message && ($player = Server::getInstance()->getPlayerExact($name)) !== null) {
                $player?->sendMessage(Practice::SERVER_PREFIX . TextFormat::GRAY . "You have left the invite for " . Practice::SERVER_COLOR . ($invite->isRanked() ? "Ranked" : "UnRanked") . " " . strtoupper($invite->getKit()));
            }
        }
    }

    function getInviteCount(): int
    {
        return count($this->invites);
    }
}