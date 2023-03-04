<?php

namespace SegaCore\Core\command\staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use SegaCore\Core\Main;
use SegaCore\Core\PlayerManager;
use pocketmine\player\GameMode;

class BuildCommand extends Command{

    private $plugin;

    public function __construct(string $name, Translatable|string $description, Main $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
        $this->setPermission('sega.build');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            if(isset($args[0])) {
                if (strtoupper($this->plugin->rank[$sender->getName()]) == "OWNER" or $sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                    if($args[0] == "on"){
                        PlayerManager::$build[$sender->getName()] = true;
                        $sender->sendMessage("Succesfuly toggle your bulid mode");
                        $sender->setGamemode(GameMode::CREATIVE());
                    } else {
                        PlayerManager::$build[$sender->getName()] = false;
                        $sender->sendMessage("Succesfuly toggle your bulid mode");
                    }
                }
            } else {
                $sender->sendMessage("Usage /build <on> | <off>");
            }
        }
    }
}