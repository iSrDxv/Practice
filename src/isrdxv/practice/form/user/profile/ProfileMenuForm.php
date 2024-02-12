<?php

namespace isrdxv\practice\form\user\profile;

use dktapps\pmforms\{
  MenuForm,
  MenuOption,
  FormIcon
};

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class ProfileMenuForm extends MenuForm
{
  
  function __construct()
  {
    parent::__construct("Profile", "Your settings and configurations for your gameplay on the server are here", [
      new MenuOption("Game", new FormIcon("https://imgs.search.brave.com/zE_GHkhRlhtBYCaqMxKLw-POR87OyWfqyl4nlvAs_GQ/rs:fit:860:0:0/g:ce/aHR0cHM6Ly9zdGF0/aWMudGhlbm91bnBy/b2plY3QuY29tL3Bu/Zy8yMjAzOTg3LTIw/MC5wbmc")),
      new MenuOption("Settings", new FormIcon("https://imgs.search.brave.com/btqwVLy1cmmpag2asXOOAXvYgVFnTpEDIZFf1iYsG7A/rs:fit:860:0:0/g:ce/aHR0cHM6Ly9pbWFn/ZXMudmV4ZWxzLmNv/bS9tZWRpYS91c2Vy/cy8zLzEzOTQyNC9p/c29sYXRlZC9wcmV2/aWV3LzAzYjIxZjY5/NWNiYmVjOTkwNTI4/ZDBiYTkzNzhmN2Mw/LWNvcnJlZ2lyLWxh/LWNvbmZpZ3VyYWNp/b24tZGUtaGVycmFt/aWVudGFzLWRlLXJl/cGFyYWNpb24ucG5n")),
      new MenuOption(TextFormat::RED . "coming soon...", new FormIcon("https://imgs.search.brave.com/onQBCUtTBuvvn1tpHCu8ZFKY6AGfatroRVBjPiuAzm0/rs:fit:500:0:0/g:ce/aHR0cHM6Ly9jZG4u/cGl4YWJheS5jb20v/cGhvdG8vMjAxNy8w/Ny8yOC8yMy8xOC9j/b21pbmctc29vbi0y/NTUwMTkwXzY0MC5q/cGc"))
      ], function(Player $player, int $selectedOption): void {
        switch($selectedOption){
          case 0:
            //$player->sendForm(new ProfileGameForm());
          break;
          case 1:
            //$player->sendForm(new ProfileSettingsForm());
          break;
          case 2:
            //$player->sendForm(new SoonForm());
          break;
        }
      });
  }
  
}