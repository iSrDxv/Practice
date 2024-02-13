<?php

namespace isrdxv\practice\command\subcommand\practice;

use isrdxv\practice\Practice;
use isrdxv\practice\handler\QueueHandler;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\Server;

final class AddQueueSC extends BaseSubCommand
{

    function __construct()
    {
        parent::__construct("addqueue", "Agregue a la queue un FakePlayer");
        $this->setPermission("practice.command");
    }

    protected function prepare(): void
    {
        $this->registerArgument(0, new RawStringArgument("name"));
    }

    function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player && isset($args["name"])) {
            $fakeplayer = Server::getInstance()->getPlayerExact($args["name"]);
            if (empty($fakeplayer)) {
                $sender->sendMessage($args["name"] . " ese nombre no existe mama huevo");
                return;
            }
            QueueHandler::getInstance()->add($fakeplayer, "Sumo", true);
        }
    }
}