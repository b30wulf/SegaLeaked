<?php

namespace SegaCore\Core\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;

class DuelTask extends Task{

    private Player $player1;
    private Player $player2;
    private bool $status;
    private int $timer;

    public function __construct(Player $player1, Player $player2){
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->status = true;
        $this->timer = 5;
        Arena::$duelTimer[$player1->getName()] = 0;
        Arena::$duelTimer[$player2->getName()] = 0;
    }

    public function onRun(): void
    {
        $player1 = $this->player1;
        $player2 = $this->player2;
        if($player1->isOnline() and $player2->isOnline()) {
            if ($this->status) {
                $player1->sendTitle(TextFormat::RED.$this->timer);
                $player2->sendTitle(TextFormat::RED.$this->timer);
                $player1->setImmobile(true);
                $player2->setImmobile(true);
                --$this->timer;
                if ($this->timer <= 0) {
                    $player1->setImmobile(false);
                    $player2->setImmobile(false);
                    $player1->sendTitle(TextFormat::GREEN ."FIGHT!");
                    $player2->sendTitle(TextFormat::GREEN."FIGHT!");
                    $this->status = false;
                }
            } else {
                ++Arena::$duelTimer[$player1->getName()];
                ++Arena::$duelTimer[$player2->getName()];
            }
        } else {
            $this->getHandler()->cancel();
        }
    }
}