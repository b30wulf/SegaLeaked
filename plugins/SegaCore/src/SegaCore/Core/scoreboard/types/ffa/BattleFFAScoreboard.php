<?php

namespace SegaCore\Core\scoreboard\types\ffa;

use pocketmine\utils\TextFormat;

use SegaCore\Core\scoreboard\Scoreboard;
use SegaCore\Core\database\DatabaseControler;
use SegaCore\Core\PlayerManager;
use pocketmine\player\Player;
use pocketmine\Server;

class BattleFFAScoreboard extends Scoreboard
{

  public function getLines(): array
  {
   $player = $this->getPlayer();
    if(array_key_exists($this->getPlayer()->getName(), PlayerManager::$damager)){

 // $damager = Server::getInstance()->getPlayerExact(PlayerManager::$damager[$this->getPlayer()->getName()])->getNetworkSession()->getPing();

}else{

  $damager = "0";

}
            return [
              "§f",
              "",
              "§cArena§f: " . TextFormat::WHITE . "Snow Rush",
              "§cCombat§f: " . PlayerManager::getTimer($player),
              "§f",
              "§cYour Ping§f: " . TextFormat::WHITE . $player->getNetworkSession()->getPing(),
            //  "§cTheir Ping§f: " . $damager,
              "",
              //"§cK§8: §f" . DatabaseControler::$kill[$this->getPlayer()->getName()] . " §cD§8: §f" . DatabaseControler::$death[$this->getPlayer()->getName()],
             // $this->kdrCalc(),
              "",
              "§f" . $this->getIp()
            ]; 
  }
   

  /*private function kdrCalc(): string
  {
    $player = $this->getPlayer();

    if (DatabaseControler::$kill[$player->getName()] > 0 and DatabaseControler::$death[$player->getName()] > 0) {
      return "§cKDR§f: " . TextFormat::WHITE . round(DatabaseControler::$kill[$player->getName()] / DatabaseControler::$death[$player->getName()]);
    } else {
      return "§cKDR§f: " . TextFormat::WHITE . DatabaseControler::$kill[$player->getName()];
    }
  }*/
} 
