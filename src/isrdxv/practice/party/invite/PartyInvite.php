<?php

declare(strict_types=1);

namespace isrdxv\practice\party\invite;

use pocketmine\player\Player;
use isrdxv\practice\party\Party;

class PartyInvite
{
	private string $from;
	
	private string $to;
	
	private string $party;

	public function __construct(Player $from, Player $to, Party $party){
		$this->from = $from->getName();
		$this->to = $to->getName();
		$this->party = $party->getName();
	}

	public function getFrom(): string{
		return $this->from;
	}

	public function getTo(): string{
		return $this->to;
	}

	public function getParty(): string{
		return $this->party;
	}
	
}