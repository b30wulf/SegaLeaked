<?php

namespace SegaCore\Core\scoreboard\types;

use SegaCore\Core\scoreboard\Scoreboard;
use SegaCore\Core\database\DatabaseControler;
use SegaCore\Core\arena\Arena;

use pocketmine\utils\TextFormat;
use pocketmine\Server;

class LobbyScoreboard extends Scoreboard 
{

  public function getLines(): array
  {
    $unnodebuff = count(Arena::$unrankqueue["nodebuff"]);
    $rnodebuff = count(Arena::$rankqueue["nodebuff"]);
    $ungapple = count(Arena::$unrankqueue["gapple"]);
    $rgapple = count(Arena::$rankqueue["gapple"]);                        
    $unfist = count(Arena::$unrankqueue["fist"]);
    $rfist = count(Arena::$rankqueue["fist"]);
    $unsumo = count(Arena::$unrankqueue["sumo"]);
    $rsumo = count(Arena::$rankqueue["sumo"]);
    $inqueue = $unnodebuff + $rnodebuff + $ungapple + $rgapple + $unfist + $rfist + $unsumo + $rsumo;

    return [
      "",
      "§cOnline§8: " . TextFormat::WHITE . count(Server::getInstance()->getOnlinePlayers()),
      "§cIn Queue: §f" . $inqueue,
      "",
      "§cK§8: §f" . DatabaseControler::$kill[$this->getPlayer()->getName()] . " §cD§8: §f" . DatabaseControler::$death[$this->getPlayer()->getName()],
      "§cCoins§8: §f" . number_format(DatabaseControler::$coins[$this->getPlayer()->getName()]),
      "§r§f",
      "§8" . $this->getIp()
    ];
  }

}