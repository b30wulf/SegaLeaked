<?php

namespace SpekledFrog\KillStreak;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;

class EventListener implements Listener {

    public $plugin;

    public function __construct(KillStreak $plugin){
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        if(!$this->plugin->getProvider()->playerExists($player)){
            $this->plugin->getProvider()->registerPlayer($player);
        }
    }

   /* public function onPlayerKill(EntityDamageByEntityEvent $event){
        $player = $event->getEntity();
        $damager = $event->getDamager();
        if($player instanceof Player){
        if ($player->getHealth() <= $event->getFinalDamage()) {
            $this->plugin->getProvider()->resetKSPoints($player);
            $this->plugin->getProvider()->addKSPoints($damager, (int)"1");
        }
        }
    }*/
}
