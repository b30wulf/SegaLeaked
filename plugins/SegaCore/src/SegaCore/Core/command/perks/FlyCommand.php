<?php

namespace SegaCore\Core\command\perks;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use SegaCore\Core\Main;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use SegaCore\Core\PlayerManager;

class FlyCommand extends Command{

    public function __construct(string $name, Translatable|string $description = "")
    {
        parent::__construct($name, $description);
        parent::setAliases(["fly"]);
        $this->setPermission('sega.fly');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
             if ($sender->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                 if($sender->hasPermission("sega.fly")){
                     $sender->setAllowFlight(true);
                     $sender->sendMessage("You are now flying!");
                     
                 } else {
                     $sender->sendMessage("You dont have permission to use this command");
                 }
             }
        } else {
            $sender->sendMessage("You are not a player");
        }
    }
}