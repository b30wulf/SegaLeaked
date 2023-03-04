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


class UnFreezeCommand extends Command{

    public function __construct(string $name, Translatable|string $description, Main $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
        parent::setAliases(["unfreeze"]);
        $this->setPermission('sega.unfreeze');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->hasPermission("sega.unfreeze")){
        if(count($args) < 1){
            $sender->sendMessage("/unfreeze <playername>");
            return true;
        }
         $player = Server::getInstance()->getPlayerExact($args[0]);
            if($player == null){
                $sender->sendMessage("Player not online");
                return;
            }
                if($player instanceof Player){
                    unset(PlayerManager::$freeze[$player->getName()]);
                    $player->sendMessage("you have been unfrozen");
                }
    }
}
}