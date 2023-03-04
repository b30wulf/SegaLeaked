<?php

namespace SegaCore\Core\task;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\bot\NodebuffBot\{NodebuffEasyBot, NodebuffMediumBot, NodebuffHardBot, NodebuffHackerBot};
use SegaCore\Core\bot\FistBot\{FistEasyBot, FistMediumBot, FistHardBot, FistHackerBot};
use SegaCore\Core\PlayerManager;

class DuelBotTask extends Task{

    /** @var Player */
    private $player;

    /** @var int */
    private $id;

    /** @var bool */
    private $status;

    /** @var int  */
    private $timer = 5;

    public function __construct(Player $player, int $id)
    {
        $this->player = $player;
        $this->id = $id;
        $this->status = true;
        Arena::$duelTimer[$player->getName()] = 0;
    }

    public function onRun(): void
    {
        if($this->player->isOnline()){
            if($this->status){
                $this->player->sendTitle(TextFormat::RED . $this->timer);
                --$this->timer;
                $this->player->setImmobile(true);
                if($this->timer <= 0){
                    $location = new Location(Arena::$posduel[1][0],Arena::$posduel[1][1], Arena::$posduel[1][2],$this->player->getWorld(), 0,0);
                    $this->player->sendTitle(TextFormat::GREEN . "FIGHT!");
                    switch ($this->id){
                        case PlayerManager::NODEBUFFBOT_EASY:
                            $bot = new NodebuffEasyBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::NODEBUFFBOT_MEDIUM:
                            $bot = new NodebuffMediumBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::NODEBUFFBOT_HARD:
                            $bot = new NodebuffHardBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::NODEBUFFBOT_HACKER:
                            $bot = new NodebuffHackerBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::FISTBOT_EASY:
                            $bot = new FistEasyBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::FISTBOT_MEDIUM:
                            $bot = new FistMediumBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::FISTBOT_HARD:
                            $bot = new FistHardBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                        case PlayerManager::FISTBOT_HACKER:
                            $bot = new FistHackerBot($location, $this->player->getSkin(), $this->player);
                            $bot->spawnToAll();
                            $bot->setCanSaveWithChunk(false);
                            $bot->setNameTagAlwaysVisible(true);
                            break;
                    }
                    $this->status = false;
                    $this->player->setImmobile(false);
                }
            } else {
                ++Arena::$duelTimer[$this->player->getName()];
            }
        } else {
            $this->getHandler()->cancel();
        }
    }
}