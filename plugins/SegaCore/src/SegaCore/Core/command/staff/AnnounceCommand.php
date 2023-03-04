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


class AnnounceCommand extends Command{

    public function __construct(string $name, Translatable|string $description, Main $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
        parent::setAliases(["announcement"]);
        $this->setPermission('sega.announce');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->hasPermission("sega.announce")){
        if(count($args) < 1){
            $sender->sendMessage("/ano <message>");
            return true;
        }
            $title = "§7[§cSE§fGA§r§7] ";
            $subtitle = implode(" ", $args);
            Server::getInstance()->broadcastMessage($title . $subtitle);
        }
    }
}