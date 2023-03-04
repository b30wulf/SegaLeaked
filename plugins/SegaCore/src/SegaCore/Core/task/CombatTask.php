<?php

namespace SegaCore\Core\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use SegaCore\Core\EventListener;
use SegaCore\Core\Main;
use SegaCore\Core\PlayerManager;
use pocketmine\Server;

class CombatTask extends Task{
    const prefix = "§l§cSE§fGA";
    private Player $player1;
    private Player $player2;
    private EventListener $listener;
    
    public function __construct(Player $player1, Player $player2, EventListener $listener)
    {
        $this->player1 = $player1;
        $this->player2 = $player2;
        $this->listener = $listener;
        PlayerManager::$iscombat[$player1->getName()] = true;
        PlayerManager::$iscombat[$player2->getName()] = true;
    }

    public function onRun(): void
    {
      $listener = $this->listener;
            if ($this->player1->isOnline() && $this->player2->isOnline() && $this->player1->isAlive() && $this->player2->isAlive()) {
                $worldname = $this->player1->getWorld()->getFolderName();
                PlayerManager::timer($this->player1);
                PlayerManager::timer($this->player2);
             if (PlayerManager::getTimer($this->player1) <= 0 && PlayerManager::getTimer($this->player2) <= 0)

{
                    PlayerManager::unsetTimer($this->player1);
                    PlayerManager::unsetTimer($this->player2);
                    PlayerManager::unsetDamager($this->player2);
                    PlayerManager::unsetDamager($this->player1);
                    $this->player1->sendMessage(self::prefix." §l§e» §r§aYou are not in combat now");
                    $this->player2->sendMessage(self::prefix." §l§e» §r§aYou are not in combat now");
                    $this->getHandler()->cancel();
                  // if(isset(PlayerManager::$hno[$this->player1->getName()])){
                   //if(isset(PlayerManager::$hno[$this->player2->getName()])){
                     foreach(Server::getInstance()->getOnlinePlayers() as $pl){
                        $this->player1->showPlayer($pl);
                        $this->player2->showPlayer($pl);
                }
                
                
             }
                if ($worldname !== null) {
                    if ($worldname == Main::getInstance()->getLobby() or $this->player2->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        PlayerManager::unsetTimer($this->player1);
                        PlayerManager::unsetTimer($this->player2);
                        PlayerManager::unsetDamager($this->player2);
                        PlayerManager::unsetDamager($this->player1);
                        $this->player1->sendMessage(self::prefix . " §l§e» §r§aYou are not in combat now");
                        $this->player2->sendMessage(self::prefix . " §l§e» §r§aYou are not in combat now");
                     foreach(Server::getInstance()->getOnlinePlayers() as $pl){
                        $this->player1->showPlayer($pl);
                        $this->player2->showPlayer($pl);
                        if(is_null($this->player1)) return;
                        if(is_null($this->player2)) return;
                         $this->getHandler()?->cancel();
                }
                
                
                    }
                }
            
            } else {
                unset(PlayerManager::$damager[$this->player1->getName()]);
                unset(PlayerManager::$damager[$this->player2->getName()]);
                unset(PlayerManager::$timer[$this->player1->getName()]);
                unset(PlayerManager::$timer[$this->player2->getName()]);
                $this->getHandler()->cancel();
                 // if(isset(PlayerManager::$hno[$this->player1->getName()])){
                   //if(isset(PlayerManager::$hno[$this->player2->getName()])){
                     foreach(Server::getInstance()->getOnlinePlayers() as $pl){
                        $this->player1->showPlayer($pl);
                        $this->player2->showPlayer($pl);
                }
                
                
            }
    }

    public function onCancel(): void
    {
       unset(PlayerManager::$iscombat[$this->player1->getName()]);
       unset(PlayerManager::$iscombat[$this->player2->getName()]);
    }
}
