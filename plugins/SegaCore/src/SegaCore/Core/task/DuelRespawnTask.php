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
             $level = $player->getWorld();
             $worldName = $level->getFolderName();
            echo($worldName);
                     if($worldName !== "lobby1" || $worldName !== "duel0" || $worldName !== "duel1" || $worldName !== "duel"){
                    Server::getInstance()->getWorldManager()->unloadWorld(Server::getInstance()->getWorldManager()->getWorldByName($worldName));
            /*$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = Server::getInstance()->getDataPath() . "/worlds/" . $worldName, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $fileInfo) {
                if ($filePath = $fileInfo->getRealPath()) {
                    if ($fileInfo->isFile()) {
                        unlink($filePath);
                    } else {
                        rmdir($filePath);
                    }
                }
            }
                     
                 
            rmdir($worldPath);*/
                Eventlistener::lobby($player);
                       } 
                    }
		}
    } 
