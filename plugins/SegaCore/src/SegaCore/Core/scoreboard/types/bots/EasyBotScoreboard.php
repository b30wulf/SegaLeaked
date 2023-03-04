<?php

namespace SegaCore\Core\scoreboard\types\bots;

use pocketmine\utils\TextFormat;

use SegaCore\Core\scoreboard\Scoreboard;
use SegaCore\Core\database\DatabaseControler;
use SegaCore\Core\PlayerManager;
use pocketmine\Server;
use pocketmine\player\Player;

class EasyBotScoreboard extends Scoreboard
{

  public function getLines(): array
  {
    $player = $this->getPlayer();
            return [
              "§f",
              "",
              "§cDifficulty§f: " . TextFormat::WHITE . "EasyBot",
              "§f",
              "§cYour Ping§f: " . TextFormat::WHITE . $player->getNetworkSession()->getPing(),
              "",
              "§cK§8: §f" . DatabaseControler::$kill[$this->getPlayer()->getName()] . " §cD§8: §f" . DatabaseControler::$death[$this->getPlayer()->getName()],
              //$this->kdrCalc(),
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