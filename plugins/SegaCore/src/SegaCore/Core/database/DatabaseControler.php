<?php

namespace SegaCore\Core\database;

use pocketmine\player\Player;
use SegaCore\Core\arena\KitManager;
use SegaCore\Core\EventListener;

class DatabaseControler extends Database{

    public static $kill = [];
    public static $death = [];
    public static $elo = [];
    public static $coins = [];
    public static $cosmetic = [];

    public static function registerPlayer(Player $player)
    {
        $playername = $player->getName();
        if (parent::getDatabase()->query("SELECT * FROM playerstats WHERE username='$playername'")->fetchArray() == null and parent::getDatabase()->query("SELECT * FROM playerkit WHERE username='$playername'")->fetchArray() == null and parent::getDatabase()->query("SELECT * FROM playersetting WHERE username='$playername'")->fetchArray() == null) {
            parent::getDatabase()->query("INSERT INTO playerstats VALUES (null, '$playername', 0, 0, 1, 0, 'DEFAULT', 100, 0)");
            parent::getDatabase()->query("INSERT INTO playerkit VALUES (null, '$playername', 'default', 'default', 'default', 'default', 'default','default', 'default')");
            parent::getDatabase()->query("INSERT INTO playersetting VALUES (null, '$playername', 0, 0 , 1)");
            $default = base64_encode(serialize(["wings" => [], "tags" => [], "sound" => [], "capes" => [], "equip" => ["capes" => "default", "wings" => "default", "tags" => "default", "sound" => "default"]]));
            parent::getDatabase()->query("INSERT INTO playercosmetic VALUES (null , '$playername', '$default')");
            KitManager::$kit["gapple"][$player->getName()] = "default";
            KitManager::$kit["fist"][$player->getName()] = "default";
            KitManager::$kit["nodebuff"][$player->getName()] = "default";
            KitManager::$kit["combo"][$player->getName()] = "default";
            KitManager::$kit["builduhc"][$player->getName()] = "default";
            KitManager::$kit["voidfight"][$player->getName()] = "default";
            KitManager::$kit["blockin"][$player->getName()] = "default";
        } else {
            $kit = parent::getDatabase()->query("SELECT * FROM playerkit WHERE username='$playername'")->fetchArray();
            //KitManager::$kit["gapple"][$player->getName()] = $kit["gapplekit"];
            KitManager::$kit["fist"][$player->getName()] = "default";
            KitManager::$kit["gapple"][$player->getName()] = "default";
            KitManager::$kit["nodebuff"][$player->getName()] = $kit["nodebuffkit"];
            KitManager::$kit["combo"][$player->getName()] = $kit["combokit"];
            KitManager::$kit["builduhc"][$player->getName()] = $kit["builduhckit"];
            KitManager::$kit["voidfight"][$player->getName()] = $kit["voidfightkit"];
            KitManager::$kit["blockin"][$player->getName()] = $kit["blockinkit"];
        }
        //$setting = parent::getDatabase()->query("SELECT * FROM playersetting WHERE username='$playername'")->fetchArray();
        $stats = parent::getDatabase()->query("SELECT * FROM playerstats WHERE username='$playername'")->fetchArray();
        $cosmetic = parent::getDatabase()->query("SELECT * FROM playercosmetic WHERE username='$playername'")->fetchArray();
        // 1x Query / Table
        self::$cosmetic[$player->getName()] = $cosmetic["cosmetics"];        
        self::$kill[$player->getName()] = $stats["kills"];
        self::$death[$player->getName()] = $stats["deaths"];
        self::$coins[$player->getName()] = $stats["coins"];
        self::$elo[$player->getName()] = $stats["elo"];
        /*EventListener::$autogg[$player->getName()] = $setting["autogg"];
        EventListener::$autoez[$player->getName()] = $setting["autoez"];
        EventListener::$cpspopup[$player->getName()] = $setting["cpspopup"];*/
    }

    public static function init(){
        parent::getDatabase()->query("CREATE TABLE IF NOT EXISTS playerstats (id INTEGER PRIMARY KEY AUTOINCREMENT , username VARCHAR(255) NOT NULL, kills INT(11) NOT NULL, deaths INT(11) NOT NULL, levels INT(11) NOT NULL, exp INT(11) NOT NULL, ranks VARCHAR(255) NOT NULL, elo INT(11) NOT NULL, coins INT(11) NOT NULL );");
        parent::getDatabase()->query("CREATE TABLE IF NOT EXISTS playerkit (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(255) NOT NULL, nodebuffkit VARCHAR(255) NOT NULL, combokit VARCHAR(255) NOT NULL, builduhckit VARCHAR(255) NOT NULL, voidfightkit VARCHAR(255) NOT NULL,blockinkit VARCHAR(255) NOT NULL ,gapplekit VARCHAR(255) NOT NULL ,fistkit VARCHAR(255) NOT NULL);");
        parent::getDatabase()->query("CREATE TABLE IF NOT EXISTS playersetting (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(255) NOT NULL, autogg INT(11) NOT NULL, autoez INT(11) NOT NULL, cpspopup INT(11) NOT NULL);");
        parent::getDatabase()->query("CREATE TABLE IF NOT EXISTS playercosmetic (id INTEGER PRIMARY KEY AUTOINCREMENT, username VARCHAR(255) NOT NULL, cosmetics TEXT NOT NULL);");
        parent::getDatabase()->query("CREATE TABLE IF NOT EXISTS guild (id INTEGER PRIMARY KEY AUTOINCREMENT, guildname VARCHAR(255) NOT NULL, guildlevel INT(11) NOT NULL, guildexp INT(11) NOT NULL);");
        parent::getDatabase()->query("CREATE TABLE IF NOT EXISTS playerguild (guildname VARCHAR(255) NOT NULL, playername VARCHAR(255) NOT NULL);");
    }

     public static function getKills(Player $player): int{
        $playername = $player->getName();
        $kills = parent::getDatabase()->query("SELECT kills FROM playerstats WHERE username='$playername'")->fetchArray();
        return $kills[0];
    }
    /* @deprecated */
    // NOT USED
    public static function addKills(Player $player){
        $playername = $player->getName();
        $kills = self::getKills($player);
        parent::getDatabase()->query("UPDATE playerstats SET kills=$kills + 1 WHERE username='$playername'");
    }

    public static function getDeath(Player $player): int{
        $playername = $player->getName();
        $death = parent::getDatabase()->query("SELECT deaths FROM playerstats WHERE username='$playername'")->fetchArray();
        return $death[0];
    }


    /* @deprecated */
    // NOT USED
    public static function addDeath(Player $player){
        $playername = $player->getName();
        $death = self::getDeath($player);
        parent::getDatabase()->query("UPDATE playerstats SET deaths=$death + 1 WHERE username='$playername'");
    }

    public static function getExp(Player $player): int{
        $playername = $player->getName();
        $exp = parent::getDatabase()->query("SELECT exp FROM playerstats WHERE username='$playername'")->fetchArray();
        return $exp[0];
    }

    public static function addExp(Player $player, int $value){
        $playername = $player->getName();
        $exp = self::getExp($player);
        parent::getDatabase()->query("UPDATE playerstats SET exp=$exp + $value WHERE username='$playername'");
    }

    public static function setExp(Player $player, int $value){
        $playername = $player->getName();
        parent::getDatabase()->query("UPDATE playerstats SET exp=$value WHERE username='$playername'");
    }

    public static function getLevel(Player $player){
        $playername = $player->getName();
        $level = parent::getDatabase()->query("SELECT levels FROM playerstats WHERE username='$playername'")->fetchArray();
        return $level[0];
    }

    public static function addLevel(Player $player){
        $playername = $player->getName();
        $level = self::getLevel($player);
        parent::getDatabase()->query("UPDATE playerstats SET levels=$level + 1 WHERE username='$playername'");
    }

    public static function getKillsByName(string $playername): int{
        $kills = parent::getDatabase()->query("SELECT kills FROM playerstats WHERE username='$playername'")->fetchArray();
        return $kills[0];
    }

    public static function addKillsByName(string $playername){
        $kill = self::getKillsByName($playername);
        parent::getDatabase()->query("UPDATE playerstats SET kills=$kill + 1 WHERE username='$playername'");
    }

    public static function getRanks(Player $player){
        $playername = $player->getName();
        $ranks = parent::getDatabase()->query("SELECT ranks FROM playerstats WHERE username='$playername'")->fetchArray();
        return $ranks[0];
    }

    public static function setRanks(string $playername, string $rank){
        parent::getDatabase()->query("UPDATE playerstats SET ranks='$rank' WHERE username='$playername'");
    }

    public static function setKill(Player $player, int $value){
        $playername = $player->getName();
        parent::getDatabase()->query("UPDATE playerstats SET kills=$value WHERE username='$playername'");
    }

    public static function setDeath(Player $player, int $value){
        $playername = $player->getName();
        parent::getDatabase()->query("UPDATE playerstats SET deaths=$value WHERE username='$playername'");
    }

    public static function getElo(Player $player){
        $playername = $player->getName();
        $elo = parent::getDatabase()->query("SELECT elo FROM playerstats WHERE username='$playername'")->fetchArray();
        return $elo[0];
    }

    public static function setElo(PLayer $player, int $value){
        $playername = $player->getName();
        parent::getDatabase()->query("UPDATE playerstats SET elo=$value WHERE username='$playername'");
    }

    public static function setCoin(Player $player, int $value){
        $playername = $player->getName();
        parent::getDatabase()->query("UPDATE playerstats SET coins=$value WHERE username='$playername'");
    }

    public static function setCosmetic(Player $player, string $value){
        $playername = $player->getName();
        parent::getDatabase()->query("UPDATE playercosmetic SET cosmetics='$value' WHERE username='$playername'");
    }
}