<?php

namespace isrdxv\practice;

use isrdxv\practice\PracticeLoader;

use pocketmine\utils\TextFormat;

class Practice
{
    static bool $maintenance = false;
    
    const RANK_SUPERIORS = ["Owner", "Admin", "Mod", "Developer", "Strom", "Zodiac", "YouTuber", "Streamer"];
    
    const BYPASS = ["SrClauYT", "StyleMH"];
    
    const SERVER_NAME = TextFormat::DARK_AQUA . "StromMC";
    
    const SERVER_COLOR = TextFormat::DARK_AQUA;
    
    const SERVER_MOTD = self::SERVER_NAME . ": " . TextFormat::BOLD . TextFormat::GOLD . "¡¡NEW RELEASE!!" . TextFormat::GRAY;

    const SERVER_MAX_PLAYERS = 2; //100

    const SERVER_PREFIX = TextFormat::BLACK . "[" . self::SERVER_NAME . TextFormat::BLACK . "]" . TextFormat::BOLD . TextFormat::GRAY . "» " . TextFormat::RESET;
    
    const BROADCAST_LIST = [
         TextFormat::RESET . TextFormat::GRAY . "Don't forget to enter our store to see the benefits: strommc.tebex.io",
         TextFormat::RESET . TextFormat::GREEN . "Don't forget to enjoy our server, and vote to get a rank for 1 month: link"
    ];
 
    static function setMaintenance(): void
    {
      self::$maintenance = PracticeLoader::getInstance()->getConfig()->getNested("maintenance");
    }
    
    static function getMaintenance(): bool
    {
      return self::$maintenance;
    }
    
}
