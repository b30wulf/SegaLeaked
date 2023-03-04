<?php

namespace SegaCore\Core;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SegaCore\Core\database\DatabaseControler;

class LevelManager{

    public static $level = [];

    public static function addLevel(Player $player){

    }

    public static function getLevelFormat(int $level){
        if ($level < 20) {
            return TextFormat::GRAY . "[$level]";
        } elseif ($level < 40) {
            return TextFormat::BLUE . "[$level]";
        } elseif ($level < 60) {
            return TextFormat::GREEN . "[$level]";
        } elseif ($level < 80) {
            return TextFormat::LIGHT_PURPLE . "[$level]";
        } elseif ($level < 100) {
            return TextFormat::YELLOW . "[$level]";
        } else {
             return TextFormat::RED . "[$level]";
        }
    }

    public static function addExp(Player $player, int $value){
        DatabaseControler::addExp($player, $value);
        if(DatabaseControler::getExp($player) >= self::getMaxExp($player)){
            $exp = DatabaseControler::getExp($player) - self::getMaxExp($player);
            DatabaseControler::setExp($player, $exp);
            DatabaseControler::addLevel($player);
            ++self::$level[$player->getName()];
        }
    }

    public static function getMaxExp(Player $player){
        return DatabaseControler::getLevel($player) * 1000;
    }
}