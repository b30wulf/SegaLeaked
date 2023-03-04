<?php

namespace SegaCore\Core\command\staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use pocketmine\Server;
use SegaCore\Core\PlayerManager;
use SegaCore\Core\Main;
use SegaCore\Core\database\DatabaseControler;


class CoinCommand extends Command{

    public function __construct(string $name, Translatable|string $description, Main $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
        parent::setAliases(["givecoin"]);
        $this->setPermission('sega.coin');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->hasPermission("sega.coin")){
     if(count($args) < 2){
         $sender->sendMessage("/givecoin <player> <amount>");
         return true;
     }
        $player = Server::getInstance()->getPlayerExact($args[0]);
        if($player instanceof Player){
            if(is_numeric($args[1])){
                $this->addCoins($player, $args[1]);
                $sender->sendMessage("Successfully given " . $args[1] . " Coins to " . $player->getName());
                return;
            } else {
                if(!is_numeric($args[1])){
                    $sender->sendMessage("The amount is not a numeric");
                } 
            }
        } else {
                if(is_null($player)){
                $sender->sendMessage($args[0] . " Is not online");
            }
        }
        }
    }
    
    public function addCoins(Player $player, int $value)
    {
        $player->sendMessage("You have given coin ammount: " . " $value");
        DatabaseControler::$coins[$player->getName()] += $value;
    }
}