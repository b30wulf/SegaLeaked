<?php

namespace SegaCore\Core;

use pocketmine\player\Player;

class PlayerManager {


    /** @var int[] */
    public static $playerstatus = [];
    public static $iscombat = [];
    public static $sChat = [];
    public static $timer = [];
    public static $cps = [];
    public static $damager = [];
    public static $sprint = [];
    public static $arenasp = [];
    public static $autoGG = [];
    public static $hsb = [];
    public static $hno = [];
    public static $nopearl = [];
    public static $build = [];
     public static $freeze = [];
    
    public const LOBBY = 0;
    public const NODEBUFF_FFA = 1;
    public const SUMO_FFA = 2;
    public const COMBO_FFA = 3;
    public const RESISTANCE_FFA = 4;
    public const NODEBUFF_DUEL_UNRANKED = 5;
    public const SUMO_DUEL = 6;
    public const BOXING_DUEL = 7;
    public const VOIDFIGHT_DUEL = 8;
    public const NODEBUFFBOT_HACKER = 9;
    public const NODEBUFFBOT_EASY = 10;
    public const NODEBUFFBOT_MEDIUM = 11;
    public const NODEBUFFBOT_HARD = 12;
    public const FIST_FFA = 13;
    public const FIST_DUEL_UNRANKED = 14;
    public const GAPPLE_FFA = 15;
    public const RESISTANCE_DUEL = 16;
    public const GAPPLE_DUEL_UNRANKED = 17;
    public const BUILD_FFA = 18;
    public const BATTLE = 19;
    public const FISTBOT_EASY = 20;
    public const FISTBOT_MEDIUM = 21;
    public const FISTBOT_HARD = 22;
    public const FISTBOT_HACKER = 23;
    public const NODEBUFF_DUEL_RANKED = 24;
    public const FIST_DUEL_RANKED = 25;
    public const GAPPLE_DUEL_RANKED = 26;

    public static function getTimer(Player $player) {
        if(!array_key_exists($player->getName(), self::$timer)) return 0;
        if(self::$timer[$player->getName()]) return self::$timer[$player->getName()];
    }

    public static function timer(Player $player){
        if(!isset(self::$timer[$player->getName()])) return;
        --self::$timer[$player->getName()];
    }

    public static function unsetTimer(Player $player) {
        unset(self::$timer[$player->getName()]);
    }

    public static function setTimer(Player $player, Player $enemy) {
        self::$timer[$player->getName()] = 10;
        self::$timer[$enemy->getName()] = 10;
    }

    public static function unsetDamager(Player $player) {
        unset(self::$damager[$player->getName()]);
    }

    public static function setEnemy(Player $player, Player $enemy) {
        self::$damager[$player->getName()] = $enemy->getName();
        self::$damager[$enemy->getName()] = $player->getName();
    }

    public static function getEnemy(Player $player) : string {
        if(!self::$damager[$player->getName()]) return "0";
        if(self::$damager[$player->getName()]) return self::$damager[$player->getName()];
    }

    public static function hasEnemy(Player $player) : bool {
        if(!self::$damager[$player->getName()]) return false;
        if(self::$damager[$player->getName()]) return true;
    }

}
