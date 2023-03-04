<?php

namespace SegaCore\Core;

use pocketmine\player\Player;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\World;
use pocketmine\world\Position;
use pocketmine\math\Vector3;
use pocketmine\Server;
use SegaCore\Core\arena\KitManager;
use SegaCore\Core\PlayerManager;

class Utils{

    public static function anvilsound(Player $player){
        $player->getWorld()->addSound($player->getPosition()->asVector3(), new AnvilFallSound());
    }
    
    public static function randomtp(Player $player){
        $manager = new KitManager();
        $world = Server::getInstance()->getWorldManager()->getWorldByName("build");
           switch(mt_rand(1,5)){
               case 1:
                   $player->teleport(new Position(211, 99, 282, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BUILD_FFA;
                    $manager->sendKit($player, PlayerManager::BUILD_FFA);
               break;
               case 2:
                   $player->teleport(new Position(225, 97, 236, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BUILD_FFA;
                   $manager->sendKit($player, PlayerManager::BUILD_FFA);
               break;
               case 3:
                   $player->teleport(new Position(197, 99, 245, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BUILD_FFA;
                    $manager->sendKit($player, PlayerManager::BUILD_FFA);
               break;
               case 4: 
                   $player->teleport(new Position(182, 111, 325, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BUILD_FFA;
                    $manager->sendKit($player, PlayerManager::BUILD_FFA);
               break;
               case 5:
                   $player->teleport(new Position(260, 102, 285, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BUILD_FFA;
                    $manager->sendKit($player, PlayerManager::BUILD_FFA);
               break;
           }
    }
    
    public static function battlepos(Player $player){
        $manager = new KitManager();
        $world = Server::getInstance()->getWorldManager()->getWorldByName("Battle");
           switch(mt_rand(1,10)){
               case 1:
                   $player->teleport(new Position(-75, 11, 0, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 2:
                   $player->teleport(new Position(-75, 11, -31, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                   $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 3:
                   $player->teleport(new Position(-31, 11, -75, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 4: 
                   $player->teleport(new Position(0, 11, -75, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 5:
                   $player->teleport(new Position(31, 11, -75, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 6:
                   $player->teleport(new Position(75, 11, -31, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 7:
                   $player->teleport(new Position(75, 11, 0, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 8:
                   $player->teleport(new Position(75, 11, 31, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 9:
                   $player->teleport(new Position(31, 11, 75, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
               case 10:
                   $player->teleport(new Position(0, 11, 75, $world));
                   PlayerManager::$playerstatus[$player->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($player, PlayerManager::BATTLE);
               break;
           }
    }
}
