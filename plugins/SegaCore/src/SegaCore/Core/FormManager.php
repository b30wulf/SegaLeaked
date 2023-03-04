<?php

namespace SegaCore\Core;

use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\Server;
use SegaCore\Core\Utils;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use pocketmine\world\World;
use SegaCore\Core\arena\KitManager;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use SegaCore\Core\bot\NodebuffBot\{NodebuffEasyBot, NodebuffMediumBot, NodebuffHardBot, NodebuffHackerBot};
use SegaCore\libs\pmquery\PMQuery;
use SegaCore\libs\pmquery\PmQueryException;
use SegaCore\Core\bot\FistBot\{FistEasyBot, FistMediumBot, FistHardBot, FistHackerBot};
use SegaCore\Core\database\DatabaseControler;
use SegaCore\libs\FormAPI\SimpleForm;
use SegaCore\libs\FormAPI\CustomForm;
use SegaCore\Core\PlayerManager;
class FormManager{

    private $price = ["cape" => ["Blue Creeper"=> 1500, "Enderman"=> 2500, "Energy" => 3500, "Fire" => 4500, "Red Creeper" => 5500, "Turtle" => 6500, "Pickaxe" => 7500, "Firework" => 8500, "Iron Golem" => 9500], "tags" => [TextFormat::YELLOW . "Warrior" => 2000,TextFormat::BLUE . "Hunter"=> 4000, TextFormat::DARK_PURPLE . "Legend" => 6000, TextFormat::WHITE . "God" . TextFormat::AQUA . "" => 8000, "§l§cGiga §fChad" => 100000]];

    private $tagslist = [TextFormat::YELLOW . "Warrior", TextFormat::BLUE . "Hunter", TextFormat::DARK_PURPLE . "Legend", TextFormat::WHITE . "God" . TextFormat::AQUA . "", "§l§cGiga §fChad"];
    
    public $playerlist = [];


    public function ffaForm(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
            $manager = new KitManager();
            switch ($data) {
                case 0:
                    if(count(Server::getInstance()->getWorldManager()->getWorldByName("nodebuff")->getPlayers()) >= 20){
                        $player->sendMessage("Max Players!!!");
                    } else {
                        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::NODEBUFF_FFA;
                        $manager->sendKit($player, PlayerManager::NODEBUFF_FFA);
                        $manager->teleportffa($player, PlayerManager::NODEBUFF_FFA);
                        $player->setFlying(false);
                        $player->setAllowFlight(false);
                        
                    }
                    break;
                case 1:
                     if(count(Server::getInstance()->getWorldManager()->getWorldByName("resistance")->getPlayers()) >= 20){
                        $player->sendMessage("Max Players!!!");
                    } else {
                        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::RESISTANCE_FFA;
                        $manager->sendKit($player, PlayerManager::RESISTANCE_FFA);
                        $manager->teleportffa($player, PlayerManager::RESISTANCE_FFA);
                         $player->setFlying(false);
                         $player->setAllowFlight(false);
                     }
                    break;
                case 2:
                     if(count(Server::getInstance()->getWorldManager()->getWorldByName("fist")->getPlayers()) >= 20){
                        $player->sendMessage("Max Players!!!");
                    } else {
                        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::FIST_FFA;
                        $manager->sendKit($player, PlayerManager::FIST_FFA);
                        $manager->teleportffa($player, PlayerManager::FIST_FFA);
                         $player->setAllowFlight(false);
                         $player->setFlying(false);
                     }
                    break;
                     case 3:
                     if(count(Server::getInstance()->getWorldManager()->getWorldByName("combo")->getPlayers()) >= 20){
                        $player->sendMessage("Max Players!!!");
                    } else {
                        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::COMBO_FFA;
                        $manager->sendKit($player, PlayerManager::COMBO_FFA);
                        $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("combo")->getSafeSpawn());
                         $player->setAllowFlight(false);
                         $player->setFlying(false);
                     }
                    break;
                case 4:
                     if(count(Server::getInstance()->getWorldManager()->getWorldByName("sumo")->getPlayers()) >= 35){
                        $player->sendMessage("Max Players!!!");
                    } else {
                        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::SUMO_FFA;
                        $manager->sendKit($player, PlayerManager::SUMO_FFA);
                        $manager->teleportffa($player, PlayerManager::SUMO_FFA);
                         $player->setAllowFlight(false);
                         $player->setFlying(false);
                     }
                    break;
                case 5:
                     if(count(Server::getInstance()->getWorldManager()->getWorldByName("gapple")->getPlayers()) >= 20){
                        $player->sendMessage("Max Players!!!");
                    } else {
                        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::GAPPLE_FFA;
                        $manager->sendKit($player, PlayerManager::GAPPLE_FFA);
                        $manager->teleportffa($player, PlayerManager::GAPPLE_FFA);
                         $player->setAllowFlight(false);
                         $player->setFlying(false);
                     }
                    break;
                  case 6:
                   if(count(Server::getInstance()->getWorldManager()->getWorldByName("build")->getPlayers()) >= 20){
                        $player->sendMessage("Max Players!!!");
                    } else {
                       Utils::randomtp($player);
                       $player->setAllowFlight(false);
                       $player->setFlying(false);
                    }
                    break;
                   case 7:
                    if(count(Server::getInstance()->getWorldManager()->getWorldByName("Battle")->getPlayers()) >= 20){
                        $player->sendMessage("Max Players!!!");
                    } else {
                       Utils::battlepos($player);
                        $player->setAllowFlight(false);
                        $player->setFlying(false);
                    }
                    break;
            }
        });
        try {
            $form->setTitle(TextFormat::RED . "FFA");
            $form->addButton("Nodebuff" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("nodebuff")->getPlayers()) . " | 20", 0, "sega/textures/gms/node");
            $form->addButton("Resistance" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("resistance")->getPlayers()) . " | 20", 0, "sega/textures/gms/resist");
            $form->addButton("Fist" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("fist")->getPlayers()) . " | 20", 0, "sega/textures/gms/fist");
            $form->addButton("Combo" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("combo")->getPlayers()) . " | 20", 0, "sega/textures/gms/combo");
            $form->addButton("Sumo" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("sumo")->getPlayers()) . " | 35", 0, "sega/textures/gms/sumo");
            $form->addButton("Gapple" . "\n" . "Playing: " . count(Server::getInstance()->getWorldManager()->getWorldByName("gapple")->getPlayers()) . " | 20", 0, "sega/textures/gms/gapple");
            $form->addButton("Build" . "\n" . "Playing: " . count(Server:: getInstance()->getWorldManager()->getWorldByName("build")->getPlayers()) ." | 20", 0, "sega/textures/gms/build");
            // $form->addButton("Snow Rush" . "\n" . "Playing: " . count(Server:: getInstance()->getWorldManager()->getWorldByName("Battle")->getPlayers()) ." | 20", 0, "textures/items/snowball");
            $player->sendForm($form);
        } catch (\Error $error){
            Server::getInstance()->getLogger()->error($error->getMessage());
            $form->setTitle(TextFormat::RED . "FFA");
            $form->addButton("Nodebuff" . "\n" . "Playing: " . TextFormat::RED . "Offline", 0, "sega/textures/gms/node");
            $form->addButton("Resistance" . "\n" . "Playing: " . TextFormat::RED . "Offline", 0, "sega/textures/gms/resist");
            $form->addButton("Fist" . "\n" . "Playing: " . TextFormat::RED . "Offline", 0, "sega/textures/gms/resist");
            $form->addButton("Combo" . "\n" . "Playing: " . TextFormat::RED . "Offline", 0, "sega/textures/gms/combo");
            $form->addButton("Sumo" . "\n" . "Playing: " . TextFormat::RED . "Offline", 0, "tsega/textures/gms/sumo");
            $form->addButton("Gapple" . "\n" . "Playing:" . TextFormat::RED . "Offline", 0, "sega/textures/gms/gapple");
            $form->addButton("Build" . "\n" . "ComingSoon" . TextFormat::RED . "Offline", 0, "sega/textures/gms/build");
            //$form->addButton("Snow Rush" . "\n" . "ComingSoon:" . TextFormat::RED . "Offline", 0, "textures/items/snowball");
            $player->sendForm($form);
         return $form;
        
        }
    }
    public function setting(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    $this->settingform($player);
                    break;
                case 1:
                    $this->economy($player);
               case 2:
                 
                    break;
            }
        });
        $form->setTitle(TextFormat::RED . "Settings");
        $form->addButton("Settings", 0, "sega/textures/duels/unranked");
        if($player->hasPermission("owners.dick")){
        $form->addButton("Give something to player", 0, "sega/textures/duels/ranked");
        }
        /*$form->addButton("Spectate §rComming Soon!", 0, "sega/texture/spectate/spec");*/
        $player->sendForm($form);
        return $form;
    }
    
    public function settingform($player){
       $form = new CustomForm(function(Player $player, array $data = null){
			if($data === null){
                return true;
            }

             if($data[1] === true){
               if(!isset(PlayerManager::$cps[$player->getName()])){
                   PlayerManager::$cps[$player->getName()] = $player->getName();
             }
                 }
          
          if($data[1] === false){
           if(isset(PlayerManager::$cps[$player->getName()])){
              unset(PlayerManager::$cps[$player->getName()]);
             }
          }
           
           if($data[2] === true){
               if(!isset(PlayerManager::$arenasp[$player->getName()])){
                   PlayerManager::$arenasp[$player->getName()] = $player->getName();
             }
          }
          
          if($data[2] === false){
           if(isset(PlayerManager::$arenasp[$player->getName()])){
              unset(PlayerManager::$arenasp[$player->getName()]);
             }
          }
          
          if($data[3] === true){
               if(!isset(PlayerManager::$autoGG[$player->getName()])){
                   PlayerManager::$autoGG[$player->getName()] = $player->getName();
             }
          }
          
          if($data[3] === false){
           if(isset(PlayerManager::$autoGG[$player->getName()])){
              unset(PlayerManager::$autoGG[$player->getName()]);
             }
          }
          
          if($data[4] === true){
                if(!isset(PlayerManager::$hsb[$player->getName()])){
                    PlayerManager::$hsb[$player->getName()] = $player->getName();
                    $packet = new RemoveObjectivePacket();
                    $packet->objectiveName = $player->getName();
                    $player->getNetworkSession()->sendDataPacket($packet);
                }
            }

            if($data[4] === false){
                if(isset(PlayerManager::$hsb[$player->getName()])){
                    unset(PlayerManager::$hsb[$player->getName()]);
                } 
            }
          /* if($data[4] === true){
               if(!isset(PlayerManager::$hno[$player->getName()])){
                   PlayerManager::$hno[$player->getName()] = $player->getName();
             }
                 }
          
          if($data[4] === false){
           if(isset(PlayerManager::$hno[$player->getName()])){
              unset(PlayerManager::$hno[$player->getName()]);
             }
          }*/
          

      });
       $form->setTitle("Settings");
       $form->addLabel("Customize Your Settings");
       $form->addToggle("CPS counter", isset(PlayerManager::$cps[$player->getName()]) ? true : false);
       $form->addToggle("Arena Respawn", isset(PlayerManager::$arenasp[$player->getName()]) ? true : false);
       $form->addToggle("Auto GG", isset(PlayerManager::$autoGG[$player->getName()]) ? true : false);
       $form->addToggle("Hide ScoreBoard", isset(PlayerManager::$hsb[$player->getName()]) ? true : false);
       //$form->addToggle("Hide Non Opponents", isset(PlayerManager::$hno[$player->getName()]) ? true : false);
       $form->sendToPlayer($player);
        return $form;
    }

    public function duelsForm(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    $this->unrankform($player);
                    break;
                case 1:
                    $this->rankform($player);
               case 2:
                 
                    break;
            }
        });
        $form->setTitle(TextFormat::RED . "Duels");
        $form->addButton("Unranked Duels", 0, "sega/textures/duels/unranked");
        $form->addButton("Ranked Duels", 0, "sega/textures/duels/ranked");
        /*$form->addButton("Spectate §rComming Soon!", 0, "sega/texture/spectate/spec");*/
        $player->sendForm($form);
        return $form;
    }
    
     public function statsform(Player $player){
       $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 1:
                    DatabaseControler::$death[$player->getName()] = 0;
                    DatabaseControler::$kill[$player->getName()] = 0;
                break;
            }
        });
        $form->setTitle("§cYour §fProfile");
        $form->setContent("§8»                   §cSE§fGA        " . "\n\n" . "§d» §cKills: §f" . DatabaseControler::$kill[$player->getName()] . "\n\n" . "§d» §cDeaths: §f" . DatabaseControler::$death[$player->getName()] . "\n\n" . "§d» §cKDR: §f" . round((DatabaseControler::$kill[$player->getName()] !== 0 ? DatabaseControler::$kill[$player->getName()] : 1) / (DatabaseControler::$death[$player->getName()] !== 0 ? DatabaseControler::$death[$player->getName()] : 1), 2) . "\n\n" . "§d» §cCoins: §f" . number_format(DatabaseControler::$coins[$player->getName()]));
        $form->addButton("Back"); 
        $form->addButton("Clear"); 
        $player->sendForm($form);
        return $form;
    } 
    
    public function botform(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    $this->nodebuffbot($player);
                    break;
                case 1:
                    $this->fistbot($player);
               /*case 2:
                 
                    break;*/
            }
        });
        $form->setTitle(TextFormat::RED . "Bot Duels");
        $form->addButton("Nodebuff Bot");
        $form->addButton("Fist Bot");
        $player->sendForm($form);
        return $form;
    }
    public function nodebuffbot(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    Arena::nodebuffbotduel($player, PlayerManager::NODEBUFFBOT_EASY);
                    $player->sendMessage("§f============\n\n    §aNodebuffEasyBot\n\n§bBot Accuracy§f:§e 40%\n§bBot Reach Distance§f:§e 3\n§bBot Attack cooldown§f:§e 8\n§bBot Pot chance§f: §e990%\n§bBot Speed§f: §e0.55\n     §f============\n\n");
                    
                    break;
                case 1:
                    Arena::nodebuffbotduel($player, PlayerManager::NODEBUFFBOT_MEDIUM);
                    $player->sendMessage("§f============\n\n    §aNodebuffMediumBot\n\n§bBot Accuracy§f:§e 50%\n§bBot Reach Distance§f:§e 3.5\n§bBot Attack cooldown§f:§e 4\n§bBot Pot chance§f: §e973%\n§bBot Speed§f: §e0.60\n     §f============\n\n");
                    break;
                case 2:
                    Arena::nodebuffbotduel($player, PlayerManager::NODEBUFFBOT_HARD);
                    $player->sendMessage("§f============\n\n    §aNodebuffHardBot\n\n§bBot Accuracy§f:§e 70%\n§bBot Reach Distance§f:§e 3.8\n§bBot Attack cooldown§f:§e 6\n§bBot Pot chance§f: §e990%\n§bBot Speed§f: §e0.65\n     §f============\n\n");
                    break;
                case 3:
                    Arena::nodebuffbotduel($player, PlayerManager::NODEBUFFBOT_HACKER);
                    $player->sendMessage("§f============\n\n    §aNodebuffHackerBot\n\n§bBot Accuracy§f:§e 95%\n§bBot Reach Distance§f:§e 4.5\n§bBot Attack cooldown§f:§e 2\n§bBot Pot chance§f: §e960%\n§bBot Speed§f: §e0.70\n     §f============\n\n");
            }
        });
        $form->setTitle(TextFormat::RED . "NodebuffBot Duels");
        $form->addButton("Easy Bot", 0, "sega/textures/bot/easy_bot");
        $form->addButton("Medium Bot", 0, "sega/textures/bot/medium_bot");
        $form->addButton("Hard Bot", 0, "sega/textures/bot/hard_bot");
        $form->addButton("Hacker Bot", 0, "sega/textures/bot/hacker_bot");
        $player->sendForm($form);
        return $form;
    }
    public function fistbot(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    Arena::fistbotduel($player, PlayerManager::FISTBOT_EASY);
                    PlayerManager::$nopearl[$player->getName()] = $player->getName();
                    break;
                case 1:
                    Arena::fistbotduel($player, PlayerManager::FISTBOT_MEDIUM);
                    PlayerManager::$nopearl[$player->getName()] = $player->getName();
                    break;
                case 2:
                    Arena::fistbotduel($player, PlayerManager::FISTBOT_HARD);
                    PlayerManager::$nopearl[$player->getName()] = $player->getName();
                    break;
                case 3:
                    Arena::fistbotduel($player, PlayerManager::FISTBOT_HACKER);
                    PlayerManager::$nopearl[$player->getName()] = $player->getName();
            }
        });
        $form->setTitle(TextFormat::RED . "FistBot Duels");
        $form->addButton("Easy Bot", 0, "sega/textures/bot/easy_bot");
        $form->addButton("Medium Bot", 0, "sega/textures/bot/medium_bot");
        $form->addButton("Hard Bot", 0, "sega/textures/bot/hard_bot");
        $form->addButton("Hacker Bot", 0, "sega/textures/bot/hacker_bot");
        $player->sendForm($form);
        return $form;
    }

    public function rankform(Player $player){
       $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    Arena::addrankQueue($player, PlayerManager::NODEBUFF_DUEL_RANKED);
                    break;
                case 1:
                    Arena::addrankQueue($player, PlayerManager::FIST_DUEL_RANKED);
                    break;
                /*case 2:
                    Arena::addrankQueue($player, PlayerManager::SUMO_DUEL);
                    break;*/
                case 2:
                    Arena::addrankQueue($player, PlayerManager::GAPPLE_DUEL_RANKED);
                    break;

            }
        });
        $form->setTitle(TextFormat::RED . "Ranked Duels");
        $form->addButton("Nodebuff" . "\n" . "Queue: " . count(Arena::$rankqueue["nodebuff"]) ?? 0, 0, "sega/textures/gms/node");
        $form->addButton("Fist" . "\n" . "Queue: " . count(Arena::$rankqueue["fist"]) ?? 0, 0, "sega/textures/gms/fist");
        //$form->addButton("Combo" . "\n" . "Queue: " . count(Arena::$rankqueue["gapple"]) ?? 0, 0, "sega/textures/gms/combo");
        //$form->addButton("Sumo" . "\n" . "Queue: " . count(Arena::$rankqueue["sumo"]) ?? 0, 0,"sega/textures/gms/sumo");
        $form->addButton("Gapple" . "\n" . "Queue: " . count(Arena::$rankqueue["gapple"]) ??  0, 0, "sega/textures/gms/gapple");
        $player->sendForm($form);
        return $form;
    }

    public function unrankform(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    Arena::addUnrankQueue($player, PlayerManager::NODEBUFF_DUEL_UNRANKED);
                    break;
                case 1:
                    Arena::addUnrankQueue($player, PlayerManager::FIST_DUEL_UNRANKED);
                    break;
                /*case 2:
                    Arena::addUnrankQueue($player, PlayerManager::SUMO_DUEL);*/
                    break;
                case 2:
                    Arena::addUnrankQueue($player, PlayerManager::GAPPLE_DUEL_UNRANKED);
                    break;

            }
        });
        $form->setTitle(TextFormat::RED . " Unranked Duels");
        $form->addButton("Nodebuff" . "\n" . "Queue: " . count(Arena::$unrankqueue["nodebuff"]) ?? 0, 0, "sega/textures/gms/node");
        $form->addButton("Fist" . "\n" . "Queue: " . count(Arena::$unrankqueue["fist"]) ?? 0, 0, "sega/textures/gms/fist");
        //$form->addButton("Combo" . "\n" . "Queue: " . count(Arena::$unrankqueue["gapple"]) ?? 0, 0, "sega/textures/gms/combo");
        //$form->addButton("Sumo" . "\n" . "Queue: " . count(Arena::$unrankqueue["sumo"]) ?? 0, 0,"sega/textures/gms/sumo");
        $form->addButton("Gapple" . "\n" . "Queue: " . count(Arena::$unrankqueue["gapple"]) ??  0, 0, "sega/textures/gms/gapple");
        $player->sendForm($form);
        return $form;
    }

    public function cosmeticshop(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    $this->capeshop($player);
                    break;
                case 1:
                    $this->tagsshop($player);
                    break;
                case 2:
                    $this->usecosmeticform($player);
                    break;
//                case 2:
//                    $this->soundshop($player);
//                    break;
            }
            return false;
        });
        $form->setTitle(TextFormat::RED . "Cosmetics");
        $form->setContent("§bCoins§f: " . number_format(DatabaseControler::$coins[$player->getName()]) . "\n\n\n\n");
        $form->addButton("Cape Shop", 0, "sega/textures/cosmetics/cape");
        $form->addButton("Tags Shop", 0, "sega/textures/cosmetics/tag");
        $form->addButton("Cosmetics Locker", 0, "sega/textures/cosmetics/backpack");
//        $form->addButton("Kill Sound Shop");
        $player->sendForm($form);
        return $form;
    }
    
     public function economy(Player $player){
        $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    $this->givecoins($player);
                    break;
             /*   case 1:
                    $this->giveelo($player);
                    break;*/
//                case 2:
//                    $this->soundshop($player);
//                    break;
            }
            return false;
        });
        $form->setTitle(TextFormat::RED . "Owners");
        $form->setContent("§bChoose what to give to the player");
        $form->addButton("Give Coins", 0, "sega/textures/cosmetics/cape");
        //$form->addButton("Give ELO", 0, "sega/textures/cosmetics/tag");
//        $form->addButton("Kill Sound Shop");
        $player->sendForm($form);
        return $form;
    }
    
    public function givecoins(Player $player) {
        $form = new CustomForm(function (Player $player, array $data = null){
            if($data === null) {
              $player->sendMessage("Giving coins failed");
                return true;
            }
            $p = Server::getInstance()->getPlayerByPrefix($data[0]);
            if($p instanceof Player){
                if(is_numeric($data[1])){
                $this->addCoins($p, $data[1]);
                $player->sendMessage("You've successfuly added " . $data[1] . " coins to " . $p->getName());
                    return;
        } else {
                if(!is_numeric($data[1])){
                    $player->sendMessage("Dicck, the amount isnt an numeric");
            }
            }
            }

        });

        $form->setTitle("Give coin");
        $form->addInput("Type a player name(Prefix)");
        $form->addInput("Input an amount(Must be numeric)");
        $form->sendToPlayer($player);

        return $form;

    }
    
   /* public function giveelo(Player $player){
        $list = [];
        foreach(Server::getInstance()->getOnlinePlayers() as $players){
            $list[] = $players->getName();
        }
        $this->playerList[$player->getName()] = $list;
        $form = new CustomForm(function(Player $player, array $data = null){
			if($data === null){
                return true;
            }
            $index = $data[1];
            $playername = $this->playerList[$player->getName()][$index];
            $p = Server::getInstance()->getPlayerExact($playername);
            if($data[2] == null){
                $player->sendMessage("Please set an amount");
                return true;
            }
            if($data[2]){
                if($p instanceof Player) {
                 $this->addElo($p, $data[2]);
                 $player->sendMessage("succesfully sent " . $data[2] .  " to " . $p->getName());
                echo("success");
                return true;
            }
               }   

        });
        $form->setTitle(TextFormat::RED . "Give elo");
        $form->addDropdown("Select a player", $this->playerList[$player->getName()]);
        $form->addInput("Amount(Must be numeric)");
        $player->sendForm($form);
        return $form;
    }*/
    
    public function addCoins(Player $player, int $value)
    {
        $player->sendMessage("You have given coin ammount: " . " $value");
        DatabaseControler::$coins[$player->getName()] += $value;
    }

    public function capeshop(Player $player){
        $form = new SimpleForm(function(Player $player, $data = null){
			if($data === null)
			{ 
				return true;
			}
            $cape = $data;
            $pdata = new Config(Main::getInstance()->getDataFolder() . "data.yml", Config::YAML);
            if(!file_exists(Main::getInstance()->getDataFolder(). $data . ".png")) {
                $player->sendMessage("The choosen cape is not available!");
                } else {
                    if(DatabaseControler::$coins[$player->getName()] >= $this->price["cape"][$cape]){
                        $array = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
                        if(!in_array($cape, $array["capes"])) {
                            $player->sendMessage(TextFormat::GREEN . "You purchased " . $cape . " for " . number_format($this->price["cape"][$cape]) . " coins");
                            $array["capes"][] = $cape;
                            $final = base64_encode(serialize($array));
                            DatabaseControler::$cosmetic[$player->getName()] = $final;
                            DatabaseControler::$coins[$player->getName()] -= $this->price["cape"][$cape];
                        } else {
                            $player->sendMessage(TextFormat::RED . "You already have " . $cape);
                        }
                    } else {
                        $player->sendMessage(TextFormat::RED . "You dont have enough coins to buy " . $cape);
                    }
                }
           
        });
        $form->setTitle("Cape Shop");
        $form->setContent("Choose your cape");
        $skinmanager = new SkinManager();
        foreach($skinmanager->getCapes() as $capes){
            $form->addButton("$capes" . "\n" . number_format($this->price["cape"][$capes]) . " Coins", -1, "", $capes);
        }
        $player->sendForm($form);
    }

    public function usecosmeticform(Player $player){
       $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    $this->usecapeform($player);
                    break;
                case 1:
                    $this->usetagsform($player);
                    break;
            }
        });
        $form->setTitle(TextFormat::RED . "Cosmetic");
        $form->addButton("Cape");
        $form->addButton("Tags");
        $player->sendForm($form);
        return $form;
    }

    public function usecapeform(Player $player){
       $form = new SimpleForm(function(Player $player, $data = null){
			if($data === null)
			{ 
				return true;
			}
            if($data === 0) {
                $oldSkin = $player->getSkin();
                $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), "", $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
                $player->setSkin($setCape);
                $player->sendSkin();
                $array = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
                $array["equip"]["capes"] = "default";
                $array = base64_encode(serialize($array));
                DatabaseControler::$cosmetic[$player->getName()] = $array;
            } else {
                $oldSkin = $player->getSkin();
                $skinmanager = new SkinManager();
                $capeData = $skinmanager->createCape($data);
                $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
                $player->setSkin($setCape);
                $player->sendSkin();
                $array = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
                $array["equip"]["capes"] = $data;
                $array = base64_encode(serialize($array));
                DatabaseControler::$cosmetic[$player->getName()] = $array;
            }
        });
        $form->setTitle(TextFormat::RED . "Cosmetic");
        $cape = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
        foreach ($cape["capes"] as $capes){
            $form->addButton("$capes", -1,"", $capes);
        }
        //$form->addButton("None", -1, "", "None");
        $player->sendForm($form);
        return $form;
    }

    public function tagsshop(Player $player){
        $form = new SimpleForm(function(Player $player, $data = null){
			if($data === null)
			{ 
				return true;
			}
            if(DatabaseControler::$coins[$player->getName()] >= $this->price["tags"][$data]) {
                $array = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
                if (!in_array($data, $array["tags"])) {
                    $array["tags"][] = $data;
                    $array = base64_encode(serialize($array));
                    DatabaseControler::$cosmetic[$player->getName()] = $array;
                    DatabaseControler::$coins[$player->getName()] -= $this->price["tags"][$data];
                    $player->sendMessage(TextFormat::GREEN . "You purchased " . $data .TextFormat::GREEN . " for " . number_format($this->price["tags"][$data]) . " coins");
                } else {
                    $player->sendMessage(TextFormat::RED . "You already have " . $data);
                }
            } else {
                $player->sendMessage(TextFormat::RED . "You dont have enough coins to buy " . $data);
            }
            return false;
        });
        $form->setTitle(TextFormat::RED . "Cosmetic");
        foreach ($this->tagslist as $tags){
            $form->addButton("$tags" . "\n" . TextFormat::RESET . $this->price["tags"][$tags] . " Coins", -1,"", $tags);
        }
        $player->sendForm($form);
        return $form;
    }

    public function usetagsform(Player $player){
        $form = new SimpleForm(function(Player $player, $data = null){
			if($data === null)
			{ 
				return true;
			}
            $array = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
            if($data == "None"){
                $array["equip"]["tags"] = "default";
                $array = base64_encode(serialize($array));
                DatabaseControler::$cosmetic[$player->getName()] = $array;
            } else {
                $array["equip"]["tags"] = $data;
                $array = base64_encode(serialize($array));
                DatabaseControler::$cosmetic[$player->getName()] = $array;
            }
        });
        $form->setTitle(TextFormat::RED . "Cosmetic");
        $tags = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
        foreach ($tags["tags"] as $tag){
            $form->addButton("$tag", -1,"", $tag);
        }
        //$form->addButton("None", -1, "", "None");
        $player->sendForm($form);
        return $form;
    }
    
        public function transfer(Player $player){
       $form = new SimpleForm(function(Player $player, int $data = null){
			if($data === null)
			{ 
				return true;
			}
			switch($data){
                case 0:
                    $ip = "185.207.250.109";
                    $port = 19132;
                    try{
                        $eu = PMQuery::query($ip, $port);
                        if($eu['Players'] < $eu['MaxPlayers']){
                            $this->tp($player, $ip, $port);
                        }else{
                            $player->sendMessage("Server Full");
                        }
                    }catch(PmQueryException $e){
                        $player->sendMessage("Server Offline");
                    }
                    break;
            }
        });
        $form->setTitle("Server Selector"); 
        try{
            $eu = PMQuery::query("185.207.250.109", 19132);
            $form->addButton("SEGA EU \n§a".$eu['Players'] ." | ". $eu['MaxPlayers']);
        }catch(PmQueryException $e){
             $form->addButton("SEGA EU\n§cOFFLINE");
        }
        $player->sendForm($form);
        return $form;
    }

    public function tp(Player $player, string $ip, int $port){
        $player->transfer($ip, $port);
    }
}
