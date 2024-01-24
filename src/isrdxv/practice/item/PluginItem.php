<?php

namespace isrdxv\practice\item;

use pocketmine\item\Item;

class PluginItem
{
  private int $slot;
  
  private Item $item;
  
  function __construct(int $slot, Item $item)
  {
    $this->slot = $slot;
    $this->item = $item;
  }

	public function getSlot(): int
	{
		return $this->slot;
	}
	
	public function getItem(): Item
	{
		return $this->item;
	}
	
}