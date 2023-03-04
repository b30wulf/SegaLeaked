<?php

namespace SegaCore\Core\task;


use SegaCore\Core\Main;
use pocketmine\scheduler\Task;
use pocketmine\block\Block;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\block\VanillaBlocks;
use SegaCore\Core\EventListener;
use pocketmine\world\World;

class BlockTask extends Task {


   protected EventListener $listener;
   protected Block $block;

   public function __construct(EventListener $listener, Block $block){

      $this->listener = $listener;
      $this->block = $block;
   }
  

    public function onRun(): void{
      $this->block->getPosition()->getWorld()->setBlock($this->block->getPosition(), VanillaBlocks::AIR()) ;


    }


}