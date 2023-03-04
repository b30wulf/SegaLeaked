<?php

declare(strict_types=1);

namespace SegaCore\Core\task;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\scheduler\Task;

class WhitelistTask extends Task{

	public function onRun(): void {
		if(Server::getInstance()->hasWhitelist()){
            Server::getInstance()->getNetwork()->setName("§l§cSE§fGA §r§cMaintenance");
        } else {
            if(!Server::getInstance()->hasWhitelist()){
            Server::getInstance()->getNetwork()->setName("§l§cSE§fGA");
            }
        }
	}
}