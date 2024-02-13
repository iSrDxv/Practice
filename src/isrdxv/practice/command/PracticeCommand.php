<?php

namespace isrdxv\practice\command;

use pocketmine\command\CommandSender;

use CortexPE\Commando\BaseCommand;
use isrdxv\practice\command\subcommand\practice\AddQueueSC;
use isrdxv\practice\Practice;
use isrdxv\practice\PracticeLoader;

use pocketmine\utils\TextFormat;

final class PracticeCommand extends BaseCommand
{

    function __construct(PracticeLoader $loader)
    {
        parent::__construct($loader, "practice", Practice::SERVER_COLOR . "COmando para los bots y otras mamadas");
        $this->setAliases(["claude"]);
        $this->setUsage("/claude <addqueue>");
    }
    
    function getPermission()
    {
      return "practice.command";
    }
    
    protected function prepare(): void
    {
    //these are its subcommands
    $this->registerSubCommand(new AddQueueSC($this->getOwningPlugin()));
    }


    function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $help = [
            TextFormat::BOLD . TextFormat::YELLOW . "Practice Command" . TextFormat::RESET,
            TextFormat::RED . "No subcommand provided, try using /" . $aliasUsed . " help"
          ];
        $sender->sendMessage(implode("\n", $help));
    }
}