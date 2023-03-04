<?php

namespace SegaCore\Core\scoreboard;

use pocketmine\network\mcpe\protocol\ClientboundPacket;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use SegaCore\Core\PlayerManager;
use pocketmine\player\Player;

abstract class Scoreboard 
{

  private Player $player;
  private string $ip = "§fsegamc.net";

  public function __construct(Player $player)
  {
    $this->player = $player;
  }

  public function getPlayer(): Player
  {
    return $this->player;
  }

  public function getIp(): string
  {
    return $this->ip;
  }

  abstract public function getLines(): array;

  private function addLine(Line $line): void 
  {
    $score = $line->getScore();
    if(!($score > 15 or $score < 1)) {
      $entry = new ScorePacketEntry();
      $entry->objectiveName = $this->player->getName();
      $entry->type = $entry::TYPE_FAKE_PLAYER;
      $entry->customName = $line->getText();
      $entry->score = $score;
      $entry->scoreboardId = $score;
      $packet = new SetScorePacket();
      $packet->type = $packet::TYPE_CHANGE;
      $packet->entries[] = $entry;
      $this->sendDataPacket($packet);
    }
  }

  public function show(): void
      {
        if(!$this->player->isOnline()) {
          return;
        }
        if(isset(PlayerManager::$hsb[$this->player->getName()])){
        if(PlayerManager::$hsb[$this->player->getName()]){
        $this->hide();
        return;
          }
        }
        $this->hide();

    $packet = new SetDisplayObjectivePacket();
    $packet->displaySlot = "sidebar";
    $packet->objectiveName = $this->player->getName();
    $packet->displayName = "logo";
    $packet->criteriaName = "dummy";
    $packet->sortOrder = 0;
    $this->sendDataPacket($packet);

    $current_number = 0;
    foreach($this->getLines() as $line) {
        $current_number++;
        $this->addLine(new Line($current_number, $line));
    }
  }

  private function hide(): void 
  {
    $packet = new RemoveObjectivePacket();
    $packet->objectiveName = $this->player->getName();
    $this->sendDataPacket($packet);
  }

  private function sendDataPacket(ClientboundPacket $packet): void
  {
    $this->player->getNetworkSession()->sendDataPacket($packet);
  }

  public static function intToString(int $int): string
  {
    $mins = floor($int / 60);
    $seconds = floor($int % 60);
    return (($mins < 10 ? "0" : "") . $mins . ":" . ($seconds < 10 ? "0" : "") . $seconds);
  }

}
