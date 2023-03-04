<?php

namespace SegaCore\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SegaCore\Core\FormManager;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use SegaCore\Core\PlayerManager;

class RegionCommand extends Command{

    public function __construct(string $name, Translatable|string $description = "")
    {
        parent::__construct($name, $description);
        parent::setAliases(["region"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player){
            $form = new FormManager();
            $form->transfer($sender);
        } else {
            $sender->sendMessage("Ur not a player");
        }
    }
}