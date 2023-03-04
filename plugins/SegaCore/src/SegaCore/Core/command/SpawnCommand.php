<?php

namespace SegaCore\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use SegaCore\Core\PlayerManager;

class SpawnCommand extends Command{

    public function __construct(string $name, Translatable|string $description = "")
    {
        parent::__construct($name, $description);
        parent::setAliases(["spawn"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $combat = PlayerManager::$iscombat[$sender->getName()] ?? false;
            if(!$combat){
                EventListener::teleportLobby($sender); 
                if($sender->hasPermission("sega.fly")){
                    $sender->setAllowFlight(true);
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "You cant go to lobby when combat!");
            }
        }
    }
}