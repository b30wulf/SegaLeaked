<?php

namespace SegaCore\Core\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use SegaCore\Core\PlayerManager;
use SegaCore\Core\EventListener;
use SegaCore\Core\scoreboard\types\duel\{FistDuelScoreboard, NodebuffDuelScoreboard, SumoDuelScoreboard};
use SegaCore\Core\scoreboard\types\ffa\{BuildFFAScoreboard, ComboFFAScoreboard, FistFFAScoreboard, GapFFAScoreboard, NodebuffFFAScoreboard, ResisFFAScoreboard, SumoFFAScoreboard, BattleFFAScoreboard};
use SegaCore\Core\scoreboard\types\LobbyScoreboard;
use SegaCore\Core\scoreboard\types\bots\EasyBotScoreboard;
use SegaCore\Core\bot\NodebuffBot\NodebuffEasyBot;
use SegaCore\Core\bot\FistBot\FistEasyBot;

class ScoreboardTask extends Task{

    public $players = [];
    private Player $player;
    private Player $player2;
    private EventListener $listener;

    const ip = "§fsegamc.net";
    const name = "logo";

    public function __construct(Player $player, Player $player2, EventListener $listener)
    {
        $this->player = $player;
        $this->player2 = $player2;
        $this->listener = $listener;
    }

    public function onRun(): void
    {
        $listener = $this->listener;
        $player2 = $this->player2;
        $player = $this->player;
        $po1 = Server::getInstance()->getPlayerByPrefix($player->getName());
        $po2 = Server::getInstance()->getPlayerByPrefix($player2->getName());
        array_push($this->players, $player,$player2);
        if($po1 && $po2){
            if(isset(PlayerManager::$playerstatus[$player->getName()])) {
                switch (PlayerManager::$playerstatus[$player->getName()]) {
                    case PlayerManager::LOBBY;
                        $scoreboard = new LobbyScoreboard($player);
                        $scoreboard->show();
                        break;
                     case PlayerManager::NODEBUFF_FFA:
                        $scoreboard = new NodebuffFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                     case PlayerManager::FIST_FFA:
                        $scoreboard = new FistFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::SUMO_FFA:
                        $scoreboard = new SumoFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::RESISTANCE_FFA:
                        $scoreboard = new ResisFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::BATTLE:
                        $scoreboard = new BattleFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::GAPPLE_FFA:
                        $scoreboard = new GapFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::BUILD_FFA:
                        $scoreboard = new BuildFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::COMBO_FFA:
                        $scoreboard = new ComboFFAScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::NODEBUFF_DUEL_UNRANKED:
                        $scoreboard = new NodebuffDuelScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::NODEBUFF_DUEL_RANKED:
                        $scoreboard = new NodebuffDuelScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::FIST_DUEL_UNRANKED:
                        $scoreboard = new FistDuelScoreboard($player);
                        $scoreboard->show();
                        break;
                   case PlayerManager::FIST_DUEL_RANKED:
                        $scoreboard = new FistDuelScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::SUMO_DUEL:
                        $scoreboard = new SumoDuelScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::FISTBOT_EASY:
                        $scoreboard = new EasyBotScoreboard($player);
                        $scoreboard->show();
                        break;
                    case PlayerManager::NODEBUFFBOT_EASY:
                        $scoreboard = new EasyBotScoreboard($player);
                        $scoreboard->show();
                        break;
                }
            }
            
        } else {
            $this->getHandler()->cancel();
        }
    }

}
