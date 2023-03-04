<?php

namespace EpicPearl\SpekledFrog\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use EpicPearl\SpekledFrog\Main;
use pocketmine\Server;

class PearlCooldown extends Task{

    protected Main $main;
	protected Player $player;
    const prefix = "§l§4SE§fGA ";

	public function __construct(Main $main, Player $player){
		$this->main = $main;
		$this->player = $player;
	}

	public function onRun() : void{
        $player = $this->player;
		if($player->isOnline()){
            $player->sendMessage(self::prefix . " §aEnderpearl cooldown finished");
            unset(Main::$pcooldown[$this->player->getName()]);
    } 
        
    }
}
