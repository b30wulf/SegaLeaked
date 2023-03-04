<?php

namespace SegaCore\Core\scoreboard\types\duel;

use SegaCore\Core\scoreboard\Scoreboard;
use SegaCore\Core\arena\Arena;

use pocketmine\utils\TextFormat;
use pocketmine\Server;

class FistDuelScoreboard extends Scoreboard 
{
  
  public function getLines(): array
  {
    $player = $this->getPlayer();

    foreach (Arena::$match as $index => $matchs) {
      foreach ($matchs as $indeks => $match) {
          if ($indeks == $player->getName()) {
              $enemy = Server::getInstance()->getPlayerExact($match);
              if ($enemy->isOnline()) {
                return [
                  "",
                  "Duel: " . TextFormat::WHITE . "Fist",
                  "Ping: " . TextFormat::WHITE . $player->getNetworkSession()->getPing(),
                  "",
                  "Opponent: " . TextFormat::WHITE . $enemy->getDisplayName(),
                  "Opponent Ping: " . TextFormat::WHITE . $enemy->getNetworkSession()->getPing(),
                  "",
                  "Duration: " . TextFormat::WHITE . self::intToString(Arena::$duelTimer[$player->getName()]),
                  "",
                  "§f" . $this->getIp()
                ];
              }
          }
      }
    }
   return [];
  }

}