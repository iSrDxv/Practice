<?php

namespace isrdxv\practice\kit;

use pocketmine\player\Player;

interface Kit
{
  function giveTo(Player $player): bool;
  
  function getName(): string;
  
  function getMainName(): string;
  
  function equals($kit): bool;
}