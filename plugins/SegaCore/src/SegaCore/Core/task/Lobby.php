<?php

namespace SegaCore\Core\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use SegaCore\Core\Main;
use pocketmine\Server;
use pocketmine\world\World;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FileSystemIterator;

class DuelRespawnTask extends Task{

    protected Eventlistener $listener;
	protected Player $player;

	public function __construct(EventListener $listener, Player $player){
		$this->listener = $listener;
		$this->player = $player;
	}

	public function onRun() : void{
        $player = $this->player;
		if($player->isOnline()){
             EventListener::teleportLobby($player);
		}
    } 
}