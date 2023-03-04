<?php

namespace SegaCore\Core\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SegaCore\Core\bot\NodebuffBot\EasyBot;

class RespawnTask extends Task{

    /** @var Player */
    private $player;

    /** @var int  */
    private $timer = 3;

    public function __construct(Player $player, Player $killer, EventListener $listener)
    {
        $this->player = $player;
        $this->killer = $killer;
        $this->listener = $listener;
    }

    public function onRun(): void
    {
        $player = $this->player;
        $klller = $this->killer;
        if($this->timer <= 0){
            // TODO RESPAWN
            $this->getHandler()->cancel();
        }
        --$this->timer;
    }

}
