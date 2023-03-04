<?php

namespace SegaCore\Core\command\staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use SegaCore\Core\PlayerManager;
use SegaCore\Core\Main;
use pocketmine\Server;


class StaffChatCommand extends Command{

    public function __construct(string $name, Translatable|string $description, Main $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
        parent::setAliases(["staffchat"]);
        $this->setPermission('sega.staff');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->hasPermission("sega.staff")){
        if(count($args) < 1){
            $sender->sendMessage("/sc on/off");
            return true;
        }
           if($sender instanceof Player){
               if(isset($args[0])){
                   switch(strtolower($args[0])){
                       case "on":
                           if(isset(PlayerManager::$sChat[$sender->getName()]))
                            {
                                $sender->sendMessage("§l§6StaffChat §e> §r§cYou already in staffchat mode");
                            }else{
                                PlayerManager::$sChat[$sender->getName()] = $sender->getName();
                                $sender->sendMessage("§l§6StaffChat §e> §r§aYou've turn on the StaffChat mode");
                           }
                       break;
                       case "off":
                           if(isset(PlayerManager::$sChat[$sender->getName()]))
                            {
                                unset(PlayerManager::$sChat[$sender->getName()]);
                                $sender->sendMessage("§l§6StaffChat §e> §r§aYou've turn off the StaffChat mode");
                            }
                            else{
                                $sender->sendMessage("§l§6StaffChat §e> §r§cYou already turn off the StaffChat mode");
                        break;
                   }
               }
           }
        } else {
               $sender->sendMessage("Use this cmd ingame");
           }
    }
}
}