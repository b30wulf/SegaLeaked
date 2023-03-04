<?php

namespace SegaCore\Core\arena;

use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\Server;
use pocketmine\world\World;
use SegaCore\Core\PlayerManager;
use pocketmine\math\Vector3;
use SegaCore\Core\task\DuelBotTask;
use SegaCore\Core\task\DuelTask;
use SegaCore\Core\Main;

class Arena
{

    public static $match = [];
    public static $unrankqueue = [];
    public static $rankqueue = [];
    public static $duelkit = [];
    public static $posduel = [[281, 95, 153], [281, 95, 238]];
    private static $possumo = [2134,24324,24342];
    private static $posvoidfight = [1231,1313,14142];
    public static $duelTimer = [];
    public static $duelindex = 0;


    public static function addUnrankQueue(Player $player, int $queueid)
    {
        $item = new ItemFactory();
        switch ($queueid) {
            case PlayerManager::NODEBUFF_DUEL_UNRANKED:
                self::$unrankqueue["nodebuff"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$unrankqueue["nodebuff"]) >= 2) {
                    self::startUnrankGame($queueid);
                }
                break;
            case PlayerManager::FIST_DUEL_UNRANKED:
                self::$unrankqueue["fist"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$unrankqueue["fist"]) >= 2) {
                    self::startUnrankGame($queueid);
                }
                break;
            case PlayerManager::RESISTANCE_DUEL:
                self::$unrankqueue["resistance"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$unrankqueue["resistance"]) >= 2) {
                    self::startUnrankGame($queueid);
                }
                break;
            case PlayerManager::VOIDFIGHT_DUEL:
                self::$unrankqueue["voidfight"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$unrankqueue["voidfight"]) >= 2) {
                    self::startUnrankGame($queueid);
                }
                break;
            case PlayerManager::SUMO_DUEL:
                self::$unrankqueue["sumo"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$unrankqueue["sumo"]) >= 2) {
                    self::startUnrankGame($queueid);
                }
                break;
            case PlayerManager::BOXING_DUEL:
                self::$unrankqueue["boxing"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$unrankqueue["boxing"]) >= 2) {
                    self::startUnrankGame($queueid);
                }
                break;
            case PlayerManager::GAPPLE_DUEL_UNRANKED:
                self::$unrankqueue["gapple"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$unrankqueue["gapple"]) >= 2) {
                    self::startUnrankGame($queueid);
                }
                break;
        }

    }

    public static function startUnrankGame(int $id)
    {

        switch ($id) {
            case PlayerManager::NODEBUFF_DUEL_UNRANKED:
                 $unrankqueue = self::$unrankqueue;
                if (isset(self::$unrankqueue["nodebuff"][0]) and isset($unrankqueue["nodebuff"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact($unrankqueue["nodebuff"][0]);
                    $p2 = Server::getInstance()->getPlayerExact($unrankqueue["nodebuff"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $unnode = Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn();
                            if($unnode === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $unnode);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset($unrankqueue["nodebuff"][0]);
                            unset($unrankqueue["nodebuff"][1]);
                            if (!isset(self::$match["unrank"]["nodebuff"])) {
                                self::$match["unrank"]["nodebuff"] = [];
                            }
                            self::$match["unrank"]["nodebuff"][$p1->getName()] = $p2->getName();
                            self::$match["unrank"]["nodebuff"][$p2->getName()] = $p1->getName();
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                 $unnode2 = Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn();
                            if($unnode2 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $unnode2);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                unset(self::$unrankqueue["nodebuff"][0]);
                                unset(self::$unrankqueue["nodebuff"][1]);
                                if (!isset(self::$match["unrank"]["nodebuff"])) {
                                    self::$match["unrank"]["nodebuff"] = [];
                                }
                                self::$match["unrank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                self::$match["unrank"]["nodebuff"][$p2->getName()] = $p1->getName();
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                      $unnode = Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn();
                            if($unnode3 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $unnode3);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                    unset(self::$unrankqueue["nodebuff"][0]);
                                    unset(self::$unrankqueue["nodebuff"][1]);
                                    if (!isset(self::$match["unrank"]["nodebuff"])) {
                                        self::$match["unrank"]["nodebuff"] = [];
                                    }
                                    self::$match["unrank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                    self::$match["unrank"]["nodebuff"][$p2->getName()] = $p1->getName();
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $unnode4 = Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn();
                            if($unnode4 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $unnode4);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                        unset(self::$unrankqueue["nodebuff"][0]);
                                        unset(self::$unrankqueue["nodebuff"][1]);
                                        if (!isset(self::$match["unrank"]["nodebuff"])) {
                                            self::$match["unrank"]["nodebuff"] = [];
                                        }
                                        self::$match["unrank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                        self::$match["unrank"]["nodebuff"][$p2->getName()] = $p1->getName();
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                              $unnode = Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn();
                            if($unnode5 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $unnode5);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$unrankqueue["nodebuff"][0]);
                                            unset(self::$unrankqueue["nodebuff"][1]);
                                            if (!isset(self::$match["unrank"]["nodebuff"])) {
                                                self::$match["unrank"]["nodebuff"] = [];
                                            }
                                            self::$match["unrank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                            self::$match["unrank"]["nodebuff"][$p2->getName()] = $p1->getName();
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);
                    }
                }
                break;
            case PlayerManager::FIST_DUEL_UNRANKED:
                if (isset(self::$unrankqueue["fist"][0]) and isset(self::$unrankqueue["nodebuff"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$unrankqueue["fist"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$unrankqueue["fist"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                         if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $fist = Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn();
                            if($fist === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $fist);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$unrankqueue["fist"][0]);
                            unset(self::$unrankqueue["fist"][1]);
                            if (!isset(self::$match["unrank"]["fist"])) {
                                self::$match["unrank"]["fist"] = [];
                            }
                            self::$match["unrank"]["fist"][$p1->getName()] = $p2->getName();
                            self::$match["unrank"]["fist"][$p2->getName()] = $p1->getName();
                        } else {
                             if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $fist2 = Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn();
                            if($fist2 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $fist2);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                unset(self::$unrankqueue["nodebuff"][0]);
                                unset(self::$unrankqueue["nodebuff"][1]);
                                if (!isset(self::$match["unrank"]["nodebuff"])) {
                                    self::$match["unrank"]["nodebuff"] = [];
                                }
                                self::$match["unrank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                self::$match["unrank"]["nodebuff"][$p2->getName()] = $p1->getName();
                            } else {
                                  if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $fist3 = Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn();
                            if($fist3 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $fist3);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                    unset(self::$unrankqueue["fist"][0]);
                                    unset(self::$unrankqueue["fist"][1]);
                                    if (!isset(self::$match["unrank"]["fist"])) {
                                        self::$match["unrank"]["fist"] = [];
                                    }
                                    self::$match["unrank"]["fist"][$p1->getName()] = $p2->getName();
                                    self::$match["unrank"]["fist"][$p2->getName()] = $p1->getName();
                                } else {
                                      if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $fist4 = Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn();
                            if($fist4 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $fist4);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                        unset(self::$unrankqueue["fist"][0]);
                                        unset(self::$unrankqueue["fist"][1]);
                                        if (!isset(self::$match["unrank"]["fist"])) {
                                            self::$match["unrank"]["fist"] = [];
                                        }
                                        self::$match["unrank"]["fist"][$p1->getName()] = $p2->getName();
                                        self::$match["unrank"]["fist"][$p2->getName()] = $p1->getName();
                                    } else {
                                         if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $fist5 = Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn();
                            if($fist5 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $fist5);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$unrankqueue["fist"][0]);
                                            unset(self::$unrankqueue["fist"][1]);
                                            if (!isset(self::$match["unrank"]["fist"])) {
                                                self::$match["unrank"]["fist"] = [];
                                            }
                                            self::$match["unrank"]["fist"][$p1->getName()] = $p2->getName();
                                            self::$match["unrank"]["fist"][$p2->getName()] = $p1->getName();
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);
                    }
                }
                break;
            case PlayerManager::RESISTANCE_DUEL:
                if (isset(self::$unrankqueue["resistance"][0]) and isset(self::$unrankqueue["resistance"][0])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$unrankqueue["resistance"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$unrankqueue["resistance"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$unrankqueue["resistance"][0]);
                            unset(self::$unrankqueue["resistance"][1]);
                            if (!isset(self::$match["unrank"]["resistance"])) {
                                self::$match["unrank"]["resistance"] = [];
                            }
                            self::$match["unrank"]["resistance"][$p1->getName()] = $p2->getName();
                            self::$match["unrank"]["resistance"][$p2->getName()] = $p1->getName();
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$unrankqueue["resistance"][0]);
                                unset(self::$unrankqueue["resistance"][1]);
                                if (!isset(self::$match["unrank"]["resistance"])) {
                                    self::$match["unrank"]["resistance"] = [];
                                }
                                self::$match["unrank"]["resistance"][$p1->getName()] = $p2->getName();
                                self::$match["unrank"]["resistance"][$p2->getName()] = $p1->getName();
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$unrankqueue["resistance"][0]);
                                    unset(self::$unrankqueue["resistance"][1]);
                                    if (!isset(self::$match["unrank"]["resistance"])) {
                                        self::$match["unrank"]["resistance"] = [];
                                    }
                                    self::$match["unrank"]["resistance"][$p1->getName()] = $p2->getName();
                                    self::$match["unrank"]["resistance"][$p2->getName()] = $p1->getName();
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$unrankqueue["resistance"][0]);
                                        unset(self::$unrankqueue["resistance"][1]);
                                        if (!isset(self::$match["unrank"]["resistance"])) {
                                            self::$match["unrank"]["resistance"] = [];
                                        }
                                        self::$match["unrank"]["resistance"][$p1->getName()] = $p2->getName();
                                        self::$match["unrank"]["resistance"][$p2->getName()] = $p1->getName();
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$unrankqueue["resistance"][0]);
                                            unset(self::$unrankqueue["resistance"][1]);
                                            if (!isset(self::$match["unrank"]["resistance"])) {
                                                self::$match["unrank"]["resistance"] = [];
                                            }
                                            self::$match["unrank"]["resistance"][$p1->getName()] = $p2->getName();
                                            self::$match["unrank"]["resistance"][$p2->getName()] = $p1->getName();
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);
                    }
                }
                break;
            case PlayerManager::BOXING_DUEL:
                if (isset(self::$unrankqueue["boxing"][0]) and isset(self::$unrankqueue["boxing"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$unrankqueue["boxing"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$unrankqueue["boxing"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$unrankqueue["boxing"][0]);
                            unset(self::$unrankqueue["boxing"][1]);
                            if (!isset(self::$match["unrank"]["boxing"])) {
                                self::$match["unrank"]["boxing"] = [];
                            }
                            self::$match["unrank"]["boxing"][$p1->getName()] = $p2->getName();
                            self::$match["unrank"]["boxing"][$p2->getName()] = $p1->getName();
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$unrankqueue["boxing"][0]);
                                unset(self::$unrankqueue["boxing"][1]);
                                if (!isset(self::$match["unrank"]["boxing"])) {
                                    self::$match["unrank"]["boxing"] = [];
                                }
                                self::$match["unrank"]["boxing"][$p1->getName()] = $p2->getName();
                                self::$match["unrank"]["boxing"][$p2->getName()] = $p1->getName();
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$unrankqueue["boxing"][0]);
                                    unset(self::$unrankqueue["boxing"][1]);
                                    if (!isset(self::$match["unrank"]["boxing"])) {
                                        self::$match["unrank"]["boxing"] = [];
                                    }
                                    self::$match["unrank"]["boxing"][$p1->getName()] = $p2->getName();
                                    self::$match["unrank"]["boxing"][$p2->getName()] = $p1->getName();
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$unrankqueue["boxing"][0]);
                                        unset(self::$unrankqueue["boxing"][1]);
                                        if (!isset(self::$match["unrank"]["boxing"])) {
                                            self::$match["unrank"]["boxing"] = [];
                                        }
                                        self::$match["unrank"]["boxing"][$p1->getName()] = $p2->getName();
                                        self::$match["unrank"]["boxing"][$p2->getName()] = $p1->getName();
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$unrankqueue["boxing"][0]);
                                            unset(self::$unrankqueue["boxing"][1]);
                                            if (!isset(self::$match["unrank"]["boxing"])) {
                                                self::$match["unrank"]["boxing"] = [];
                                            }
                                            self::$match["unrank"]["boxing"][$p1->getName()] = $p2->getName();
                                            self::$match["unrank"]["boxing"][$p2->getName()] = $p1->getName();
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);

                    }
                }
                break;
            case PlayerManager::SUMO_DUEL:
                if (isset(self::$unrankqueue["sumo"][0]) and isset(self::$unrankqueue["sumo"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$unrankqueue["sumo"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$unrankqueue["sumo"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel")->getSafeSpawn());
                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel")->getSafeSpawn());
                            $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                            $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$unrankqueue["sumo"][0]);
                            unset(self::$unrankqueue["sumo"][1]);
                            if (!isset(self::$match["unrank"]["sumo"])) {
                                self::$match["unrank"]["sumo"] = [];
                            }
                            self::$match["unrank"]["sumo"][$p1->getName()] = $p2->getName();
                            self::$match["unrank"]["sumo"][$p2->getName()] = $p1->getName();
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel2")->getSafeSpawn());
                                $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel2")->getSafeSpawn());
                                $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$unrankqueue["sumo"][0]);
                                unset(self::$unrankqueue["sumo"][1]);
                                if (!isset(self::$match["unrank"]["sumo"])) {
                                    self::$match["unrank"]["sumo"] = [];
                                }
                                self::$match["unrank"]["sumo"][$p1->getName()] = $p2->getName();
                                self::$match["unrank"]["sumo"][$p2->getName()] = $p1->getName();
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel3")->getSafeSpawn());
                                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel3")->getSafeSpawn());
                                    $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                    $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$unrankqueue["sumo"][0]);
                                    unset(self::$unrankqueue["sumo"][1]);
                                    if (!isset(self::$match["unrank"]["sumo"])) {
                                        self::$match["unrank"]["sumo"] = [];
                                    }
                                    self::$match["unrank"]["sumo"][$p1->getName()] = $p2->getName();
                                    self::$match["unrank"]["sumo"][$p2->getName()] = $p1->getName();
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel4")->getSafeSpawn());
                                        $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel4")->getSafeSpawn());
                                        $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                        $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$unrankqueue["sumo"][0]);
                                        unset(self::$unrankqueue["sumo"][1]);
                                        if (!isset(self::$match["unrank"]["sumo"])) {
                                            self::$match["unrank"]["sumo"] = [];
                                        }
                                        self::$match["unrank"]["sumo"][$p1->getName()] = $p2->getName();
                                        self::$match["unrank"]["sumo"][$p2->getName()] = $p1->getName();
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel5")->getSafeSpawn());
                                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumoduel5")->getSafeSpawn());
                                            $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                            $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$unrankqueue["sumo"][0]);
                                            unset(self::$unrankqueue["sumo"][1]);
                                            if (!isset(self::$match["unrank"]["sumo"])) {
                                                self::$match["unrank"]["sumo"] = [];
                                            }
                                            self::$match["unrank"]["sumo"][$p1->getName()] = $p2->getName();
                                            self::$match["unrank"]["sumo"][$p2->getName()] = $p1->getName();
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);

                    }
                }
                break;
            case PlayerManager::VOIDFIGHT_DUEL:
                if (isset(self::$unrankqueue["voidfight"][0]) and isset(self::$unrankqueue["voidfight"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$unrankqueue["voidfight"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$unrankqueue["voidfight"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        ArenaResetter::reset("voidfight");
                        if(Server::getInstance()->getWorldManager()->getWorldByName("voidfight")->isLoaded()) {
                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("voidfight")->getSafeSpawn());
                            $p2->telepor(Server::getInstance()->getWorldManager()->getWorldByName("voidfight")->getSafeSpawn());
                            $p1->teleport(new Vector3(self::$posvoidfight[0], self::$posvoidfight[1]));
                            $p2->teleport(new Vector3(self::$posvoidfight[0], self::$posvoidfight[1]));
                        }
                    }
                }
                break;
            case PlayerManager::GAPPLE_DUEL_UNRANKED:
                if (isset(self::$unrankqueue["gapple"][0]) and isset(self::$unrankqueue["gapple"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$unrankqueue["gapple"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$unrankqueue["gapple"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $ungapple = Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn();
                            if($ungapple === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $ungapple);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$unrankqueue["gapple"][0]);
                            unset(self::$unrankqueue["gapple"][1]);
                            if (!isset(self::$match["unrank"]["gapple"])) {
                                self::$match["unrank"]["gapple"] = [];
                            }
                            self::$match["unrank"]["gapple"][$p1->getName()] = $p2->getName();
                            self::$match["unrank"]["gapple"][$p2->getName()] = $p1->getName();
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                $ungapple2 = Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn();
                            if($ungapple2 === null){
                                return;
                            }
                           $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $ungapple2);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$unrankqueue["gapple"][0]);
                                unset(self::$unrankqueue["gapple"][1]);
                                if (!isset(self::$match["unrank"]["gapple"])) {
                                    self::$match["unrank"]["gapple"] = [];
                                }
                                self::$match["unrank"]["gapple"][$p1->getName()] = $p2->getName();
                                self::$match["unrank"]["gapple"][$p2->getName()] = $p1->getName();
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                    $ungapple3 = Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn();
                            if($ungapple3 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $ungapple3);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$unrankqueue["gapple"][0]);
                                    unset(self::$unrankqueue["gapple"][1]);
                                    if (!isset(self::$match["unrank"]["gapple"])) {
                                        self::$match["unrank"]["gapple"] = [];
                                    }
                                    self::$match["unrank"]["gapple"][$p1->getName()] = $p2->getName();
                                    self::$match["unrank"]["gapple"][$p2->getName()] = $p1->getName();
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $ungapple4 = Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn();
                            if($ungapple4 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $ungapple4);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$unrankqueue["gapple"][0]);
                                        unset(self::$unrankqueue["gapple"][1]);
                                        if (!isset(self::$match["unrank"]["gapple"])) {
                                            self::$match["unrank"]["gapple"] = [];
                                        }
                                        self::$match["unrank"]["gapple"][$p1->getName()] = $p2->getName();
                                        self::$match["unrank"]["gapple"][$p2->getName()] = $p1->getName();
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                            $ungapple5 = Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn();
                            if($ungapple5 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $ungapple5);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$unrankqueue["gapple"][0]);
                                            unset(self::$unrankqueue["gapple"][1]);
                                            if (!isset(self::$match["unrank"]["gapple"])) {
                                                self::$match["unrank"]["gapple"] = [];
                                            }
                                            self::$match["unrank"]["gapple"][$p1->getName()] = $p2->getName();
                                            self::$match["unrank"]["gapple"][$p2->getName()] = $p1->getName();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);
                }
        }
        ++self::$duelindex;
        try {
            WorldUtils::duplicateWorld("duel0", "dpduel" . self::$duelindex);
            WorldUtils::renameWorld("dpduel" . self::$duelindex, "duel" . self::$duelindex);
        } catch (\ErrorException){
        }
        $ids = [PlayerManager::BOXING_DUEL => "boxing", PlayerManager::FIST_DUEL_UNRANKED => "fist", PlayerManager::NODEBUFF_DUEL_UNRANKED => "nodebuff", PlayerManager::SUMO_DUEL => "sumo", PlayerManager::VOIDFIGHT_DUEL => "voidfight", PlayerManager::GAPPLE_DUEL_UNRANKED => "gapple"];
        $manager = new KitManager();
        $index = [];
        foreach (self::$unrankqueue[$ids[$id]] as $key => $value) {
            $index[] = $key;
        }
        if (count($index) == 2) {
            $p1 = Server::getInstance()->getPlayerExact(self::$unrankqueue[$ids[$id]][$index[0]]);
            $p2 = Server::getInstance()->getPlayerExact(self::$unrankqueue[$ids[$id]][$index[1]]);
            if ($p1->isOnline() and $p2->isOnline()) {
                if ($id == PlayerManager::VOIDFIGHT_DUEL) {
                    ArenaResetter::reset("voidfight");
                    Server::getInstance()->getWorldManager()->loadWorld("voidfight" . ArenaResetter::$index["voidfight"]);
                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("voidfight" . ArenaResetter::$index["voidfight"])->getSafeSpawn());
                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("voidfight" . ArenaResetter::$index["voidfight"])->getSafeSpawn());
                    $p1->teleport(new Vector3(self::$posduel[0][0], self::$posduel[0][1], self::$posduel[0][2]));
                    $p2->teleport(new Vector3(self::$posduel[1][0], self::$posduel[1][1], self::$posduel[1][2]));
                    $manager->sendDuelKit($p1, $id);
                    $manager->sendDuelKit($p2, $id);
                    unset(self::$unrankqueue[$ids[$id]][$index[0]]);
                    unset(self::$unrankqueue[$ids[$id]][$index[1]]);
                    if (!isset(self::$match["unrank"][$ids[$id]])) {
                        self::$match["unrank"][$ids[$id]] = [];
                    }
                } else {
                    Server::getInstance()->getWorldManager()->loadWorld("duel" . self::$duelindex);
                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel" . self::$duelindex)->getSafeSpawn());
                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel" . self::$duelindex)->getSafeSpawn());
                    $p1->teleport(new Vector3(self::$posduel[0][0], self::$posduel[0][1], self::$posduel[0][2]));
                    $p2->teleport(new Vector3(self::$posduel[1][0], self::$posduel[1][1], self::$posduel[1][2]));
                    $manager->sendDuelKit($p1, $id);
                    $manager->sendDuelKit($p2, $id);
                    unset(self::$unrankqueue[$ids[$id]][$index[0]]);
                    unset(self::$unrankqueue[$ids[$id]][$index[1]]);
                    if (!isset(self::$match["unrank"][$ids[$id]])) {
                        self::$match["unrank"][$ids[$id]] = [];
                    }
                }
                    self::$match["unrank"][$ids[$id]][$p1->getName()] = $p2->getName();
                    self::$match["unrank"][$ids[$id]][$p2->getName()] = $p1->getName();
                    PlayerManager::$playerstatus[$p1->getName()] = $id;
                    PlayerManager::$playerstatus[$p2->getName()] = $id;
                    Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);
            }
        }
    }

    public static function startrankGame(int $id)
    {
        switch ($id) {
            case PlayerManager::NODEBUFF_DUEL_RANKED:
                if (isset(self::$rankqueue["nodebuff"][0]) and isset(self::$rankqueue["nodebuff"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue["nodebuff"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue["nodebuff"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                         if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rnode = Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn();
                            if($rnode === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rnode);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$rankqueue["nodebuff"][0]);
                            unset(self::$rankqueue["nodebuff"][1]);
                            if (!isset(self::$match["rank"]["nodebuff"])) {
                                self::$match["rank"]["nodebuff"] = [];
                            }
                            self::$match["rank"]["nodebuff"][$p1->getName()] = $p2->getName();
                            self::$match["rank"]["nodebuff"][$p2->getName()] = $p1->getName();
                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                        } else {
                             if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rnode2 = Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn();
                            if($rnode2 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rnode2);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                unset(self::$rankqueue["nodebuff"][0]);
                                unset(self::$rankqueue["nodebuff"][1]);
                                if (!isset(self::$match["rank"]["nodebuff"])) {
                                    self::$match["rank"]["nodebuff"] = [];
                                }
                                self::$match["rank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                self::$match["rank"]["nodebuff"][$p2->getName()] = $p1->getName();
                                PlayerManager::$playerstatus[$p1->getName()] = $id;
                                PlayerManager::$playerstatus[$p2->getName()] = $id;
                            } else {
                                 if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rnode3 = Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn();
                            if($rnode3 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rnode3);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                    unset(self::$rankqueue["nodebuff"][0]);
                                    unset(self::$rankqueue["nodebuff"][1]);
                                    if (!isset(self::$match["rank"]["nodebuff"])) {
                                        self::$match["rank"]["nodebuff"] = [];
                                    }
                                    self::$match["rank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                    self::$match["rank"]["nodebuff"][$p2->getName()] = $p1->getName();
                                    PlayerManager::$playerstatus[$p1->getName()] = $id;
                                    PlayerManager::$playerstatus[$p2->getName()] = $id;
                                } else {
                                     if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rnode4 = Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn();
                            if($rnode4 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rnode4);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                        unset(self::$rankqueue["nodebuff"][0]);
                                        unset(self::$rankqueue["nodebuff"][1]);
                                        if (!isset(self::$match["rank"]["nodebuff"])) {
                                            self::$match["rank"]["nodebuff"] = [];
                                        }
                                        self::$match["rank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                        self::$match["rank"]["nodebuff"][$p2->getName()] = $p1->getName();
                                        PlayerManager::$playerstatus[$p1->getName()] = $id;
                                        PlayerManager::$playerstatus[$p2->getName()] = $id;
                                    } else {
                                         if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rnode5 = Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn();
                            if($rnode5 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rnode5);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$rankqueue["nodebuff"][0]);
                                            unset(self::$rankqueue["nodebuff"][1]);
                                            if (!isset(self::$match["rank"]["nodebuff"])) {
                                                self::$match["rank"]["nodebuff"] = [];
                                            }
                                            self::$match["rank"]["nodebuff"][$p1->getName()] = $p2->getName();
                                            self::$match["rank"]["nodebuff"][$p2->getName()] = $p1->getName();
                                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);

                    }
                }
                break;
            case PlayerManager::FIST_DUEL_RANKED:
                if (isset(self::$rankqueue["fist"][0]) and isset(self::$rankqueue["fist"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue["fist"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue["fist"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                         if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rfist = Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn();
                            if($rfist === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rfist);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$rankqueue["fist"][0]);
                            unset(self::$rankqueue["fist"][1]);
                            if (!isset(self::$match["rank"]["fist"])) {
                                self::$match["rank"]["fist"] = [];
                            }
                            self::$match["rank"]["fist"][$p1->getName()] = $p2->getName();
                            self::$match["rank"]["fist"][$p2->getName()] = $p1->getName();
                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rfist2 = Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn();
                            if($rfist2 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rfist2);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                unset(self::$rankqueue["fist"][0]);
                                unset(self::$rankqueue["fist"][1]);
                                if (!isset(self::$match["rank"]["fist"])) {
                                    self::$match["rank"]["fist"] = [];
                                }
                                self::$match["rank"]["fist"][$p1->getName()] = $p2->getName();
                                self::$match["rank"]["fist"][$p2->getName()] = $p1->getName();
                                PlayerManager::$playerstatus[$p1->getName()] = $id;
                                PlayerManager::$playerstatus[$p2->getName()] = $id;
                            } else {
                               if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rfist3 = Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn();
                            if($rfist3 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rfist3);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                    unset(self::$rankqueue["fist"][0]);
                                    unset(self::$rankqueue["fist"][1]);
                                    if (!isset(self::$match["rank"]["fist"])) {
                                        self::$match["rank"]["fist"] = [];
                                    }
                                    self::$match["rank"]["fist"][$p1->getName()] = $p2->getName();
                                    self::$match["rank"]["fist"][$p2->getName()] = $p1->getName();
                                    PlayerManager::$playerstatus[$p1->getName()] = $id;
                                    PlayerManager::$playerstatus[$p2->getName()] = $id;
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rfist4 = Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn();
                            if($rfist4 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rfist4);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                        unset(self::$rankqueue["fist"][0]);
                                        unset(self::$rankqueue["fist"][1]);
                                        if (!isset(self::$match["rank"]["fist"])) {
                                            self::$match["rank"]["fist"] = [];
                                        }
                                        self::$match["rank"]["fist"][$p1->getName()] = $p2->getName();
                                        self::$match["rank"]["fist"][$p2->getName()] = $p1->getName();
                                        PlayerManager::$playerstatus[$p1->getName()] = $id;
                                        PlayerManager::$playerstatus[$p2->getName()] = $id;
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rfist5 = Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn();
                            if($rfist5 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rfist5);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$rankqueue["fist"][0]);
                                            unset(self::$rankqueue["fist"][1]);
                                            if (!isset(self::$match["rank"]["fist"])) {
                                                self::$match["rank"]["fist"] = [];
                                            }
                                            self::$match["rank"]["fist"][$p1->getName()] = $p2->getName();
                                            self::$match["rank"]["fist"][$p2->getName()] = $p1->getName();
                                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);

                    }
                }
                break;
            case PlayerManager::RESISTANCE_DUEL:
                if (isset(self::$rankqueue["resistance"][0]) and isset(self::$rankqueue["resistance"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue["resistance"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue["resistance"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$rankqueue["resistance"][0]);
                            unset(self::$rankqueue["resistance"][1]);
                            if (!isset(self::$match["rank"]["resistance"])) {
                                self::$match["rank"]["resistance"] = [];
                            }
                            self::$match["rank"]["resistance"][$p1->getName()] = $p2->getName();
                            self::$match["rank"]["resistance"][$p2->getName()] = $p1->getName();
                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$rankqueue["resistance"][0]);
                                unset(self::$rankqueue["resistance"][1]);
                                if (!isset(self::$match["rank"]["resistance"])) {
                                    self::$match["rank"]["resistance"] = [];
                                }
                                self::$match["rank"]["resistance"][$p1->getName()] = $p2->getName();
                                self::$match["rank"]["resistance"][$p2->getName()] = $p1->getName();
                                PlayerManager::$playerstatus[$p1->getName()] = $id;
                                PlayerManager::$playerstatus[$p2->getName()] = $id;
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$rankqueue["resistance"][0]);
                                    unset(self::$rankqueue["resistance"][1]);
                                    if (!isset(self::$match["rank"]["resistance"])) {
                                        self::$match["rank"]["resistance"] = [];
                                    }
                                    self::$match["rank"]["resistance"][$p1->getName()] = $p2->getName();
                                    self::$match["rank"]["resistance"][$p2->getName()] = $p1->getName();
                                    PlayerManager::$playerstatus[$p1->getName()] = $id;
                                    PlayerManager::$playerstatus[$p2->getName()] = $id;
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$rankqueue["resistance"][0]);
                                        unset(self::$rankqueue["resistance"][1]);
                                        if (!isset(self::$match["rank"]["resistance"])) {
                                            self::$match["rank"]["resistance"] = [];
                                        }
                                        self::$match["rank"]["resistance"][$p1->getName()] = $p2->getName();
                                        self::$match["rank"]["resistance"][$p2->getName()] = $p1->getName();
                                        PlayerManager::$playerstatus[$p1->getName()] = $id;
                                        PlayerManager::$playerstatus[$p2->getName()] = $id;
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$rankqueue["resistance"][0]);
                                            unset(self::$rankqueue["resistance"][1]);
                                            if (!isset(self::$match["rank"]["resistance"])) {
                                                self::$match["rank"]["resistance"] = [];
                                            }
                                            self::$match["rank"]["resistance"][$p1->getName()] = $p2->getName();
                                            self::$match["rank"]["resistance"][$p2->getName()] = $p1->getName();
                                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);

                    }
                }
                break;
            case PlayerManager::BOXING_DUEL:
                if (isset(self::$rankqueue["boxing"][0]) and isset(self::$rankqueue["boxing"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue["boxing"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue["boxing"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$rankqueue["boxing"][0]);
                            unset(self::$rankqueue["boxing"][1]);
                            if (!isset(self::$match["rank"]["boxing"])) {
                                self::$match["rank"]["boxing"] = [];
                            }
                            self::$match["rank"]["boxing"][$p1->getName()] = $p2->getName();
                            self::$match["rank"]["boxing"][$p2->getName()] = $p1->getName();
                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$rankqueue["boxing"][0]);
                                unset(self::$rankqueue["boxing"][1]);
                                if (!isset(self::$match["rank"]["boxing"])) {
                                    self::$match["rank"]["boxing"] = [];
                                }
                                self::$match["rank"]["boxing"][$p1->getName()] = $p2->getName();
                                self::$match["rank"]["boxing"][$p2->getName()] = $p1->getName();
                                PlayerManager::$playerstatus[$p1->getName()] = $id;
                                PlayerManager::$playerstatus[$p2->getName()] = $id;
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$rankqueue["boxing"][0]);
                                    unset(self::$rankqueue["boxing"][1]);
                                    if (!isset(self::$match["rank"]["boxing"])) {
                                        self::$match["rank"]["boxing"] = [];
                                    }
                                    self::$match["rank"]["boxing"][$p1->getName()] = $p2->getName();
                                    self::$match["rank"]["boxing"][$p2->getName()] = $p1->getName();
                                    PlayerManager::$playerstatus[$p1->getName()] = $id;
                                    PlayerManager::$playerstatus[$p2->getName()] = $id;
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$rankqueue["boxing"][0]);
                                        unset(self::$rankqueue["boxing"][1]);
                                        if (!isset(self::$match["rank"]["boxing"])) {
                                            self::$match["rank"]["boxing"] = [];
                                        }
                                        self::$match["rank"]["boxing"][$p1->getName()] = $p2->getName();
                                        self::$match["rank"]["boxing"][$p2->getName()] = $p1->getName();
                                        PlayerManager::$playerstatus[$p1->getName()] = $id;
                                        PlayerManager::$playerstatus[$p2->getName()] = $id;
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p1->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $p2->teleport(new Vector3(self::$posduel[0],self::$posduel[1]));
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$rankqueue["boxing"][0]);
                                            unset(self::$rankqueue["boxing"][1]);
                                            if (!isset(self::$match["rank"]["boxing"])) {
                                                self::$match["rank"]["boxing"] = [];
                                            }
                                            self::$match["rank"]["boxing"][$p1->getName()] = $p2->getName();
                                            self::$match["rank"]["boxing"][$p2->getName()] = $p1->getName();
                                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);

                    }
                }
                break;
            case PlayerManager::SUMO_DUEL:
                if (isset(self::$rankqueue["sumo"][0]) and isset(self::$rankqueue["sumo"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue["sumo"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue["sumo"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn());
                            $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                            $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$rankqueue["sumo"][0]);
                            unset(self::$rankqueue["sumo"][1]);
                            if (!isset(self::$match["rank"]["sumo"])) {
                                self::$match["rank"]["sumo"] = [];
                            }
                            self::$match["rank"]["sumo"][$p1->getName()] = $p2->getName();
                            self::$match["rank"]["sumo"][$p2->getName()] = $p1->getName();
                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                        } else {
                            if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                                $manager = new KitManager();
                                $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn());
                                $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$rankqueue["sumo"][0]);
                                unset(self::$rankqueue["sumo"][1]);
                                if (!isset(self::$match["rank"]["sumo"])) {
                                    self::$match["rank"]["sumo"] = [];
                                }
                                self::$match["rank"]["sumo"][$p1->getName()] = $p2->getName();
                                self::$match["rank"]["sumo"][$p2->getName()] = $p1->getName();
                                PlayerManager::$playerstatus[$p1->getName()] = $id;
                                PlayerManager::$playerstatus[$p2->getName()] = $id;
                            } else {
                                if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                                    $manager = new KitManager();
                                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn());
                                    $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                    $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$rankqueue["sumo"][0]);
                                    unset(self::$rankqueue["sumo"][1]);
                                    if (!isset(self::$match["rank"]["sumo"])) {
                                        self::$match["rank"]["sumo"] = [];
                                    }
                                    self::$match["rank"]["sumo"][$p1->getName()] = $p2->getName();
                                    self::$match["rank"]["sumo"][$p2->getName()] = $p1->getName();
                                    PlayerManager::$playerstatus[$p1->getName()] = $id;
                                    PlayerManager::$playerstatus[$p2->getName()] = $id;
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                                        $manager = new KitManager();
                                        $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn());
                                        $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                        $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$rankqueue["sumo"][0]);
                                        unset(self::$rankqueue["sumo"][1]);
                                        if (!isset(self::$match["rank"]["sumo"])) {
                                            self::$match["rank"]["sumo"] = [];
                                        }
                                        self::$match["rank"]["sumo"][$p1->getName()] = $p2->getName();
                                        self::$match["rank"]["sumo"][$p2->getName()] = $p1->getName();
                                        PlayerManager::$playerstatus[$p1->getName()] = $id;
                                        PlayerManager::$playerstatus[$p2->getName()] = $id;
                                    } else {
                                        if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                                            $manager = new KitManager();
                                            $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn());
                                            $p1->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                            $p2->teleport(new Vector3(self::$possumo[0],self::$possumo[1]));
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$rankqueue["sumo"][0]);
                                            unset(self::$rankqueue["sumo"][1]);
                                            if (!isset(self::$match["rank"]["sumo"])) {
                                                self::$match["rank"]["sumo"] = [];
                                            }
                                            self::$match["rank"]["sumo"][$p1->getName()] = $p2->getName();
                                            self::$match["rank"]["sumo"][$p2->getName()] = $p1->getName();
                                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);

                    }
                }
                break;
            case PlayerManager::VOIDFIGHT_DUEL:
                if (isset(self::$rankqueue["voidfight"][0]) and isset(self::$rankqueue["voidfight"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue["voidfight"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue["voidfight"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                        ArenaResetter::reset("voidfight");
                        $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("voidfight" . ArenaResetter::$index["voidfight"])->getSafeSpawn());
                        $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("voidfight" . ArenaResetter::$index["voidfight"])->getSafeSpawn());
                        $p1->teleport(new Vector3(self::$posvoidfight[0],self::$posvoidfight[1]));
                        $p2->teleport(new Vector3(self::$posvoidfight[0],self::$posvoidfight[1]));
                    }
                }
                break;
            case PlayerManager::GAPPLE_DUEL_RANKED:
                if (isset(self::$rankqueue["gapple"][0]) and isset(self::$rankqueue["gapple"][1])) {
                    $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue["gapple"][0]);
                    $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue["gapple"][1]);
                    if ($p1->isOnline() and $p2->isOnline()) {
                         if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rgapple = Server::getInstance()->getWorldManager()->getWorldByName("duel")->getSafeSpawn();
                            if($rgapple === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rgapple);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                            $manager->sendDuelKit($p1, $id);
                            $manager->sendDuelKit($p2, $id);
                            unset(self::$rankqueue["gapple"][0]);
                            unset(self::$rankqueue["gapple"][1]);
                            if (!isset(self::$match["rank"]["gapple"])) {
                                self::$match["rank"]["gapple"] = [];
                            }
                            self::$match["rank"]["gapple"][$p1->getName()] = $p2->getName();
                            self::$match["rank"]["gapple"][$p2->getName()] = $p1->getName();
                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                        } else {
                             if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rgapple2 = Server::getInstance()->getWorldManager()->getWorldByName("duel2")->getSafeSpawn();
                            if($rgapple2 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rgapple2);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                $manager->sendDuelKit($p1, $id);
                                $manager->sendDuelKit($p2, $id);
                                unset(self::$rankqueue["gapple"][0]);
                                unset(self::$rankqueue["gapple"][1]);
                                if (!isset(self::$match["rank"]["gapple"])) {
                                    self::$match["rank"]["gapple"] = [];
                                }
                                self::$match["rank"]["gapple"][$p1->getName()] = $p2->getName();
                                self::$match["rank"]["gapple"][$p2->getName()] = $p1->getName();
                                PlayerManager::$playerstatus[$p1->getName()] = $id;
                                PlayerManager::$playerstatus[$p2->getName()] = $id;
                            } else {
                                 if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rgapple3 = Server::getInstance()->getWorldManager()->getWorldByName("duel3")->getSafeSpawn();
                            if($rgapple3 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rgapple3);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                    $manager->sendDuelKit($p1, $id);
                                    $manager->sendDuelKit($p2, $id);
                                    unset(self::$rankqueue["gapple"][0]);
                                    unset(self::$rankqueue["gapple"][1]);
                                    if (!isset(self::$match["rank"]["gapple"])) {
                                        self::$match["rank"]["gapple"] = [];
                                    }
                                    self::$match["rank"]["gapple"][$p1->getName()] = $p2->getName();
                                    self::$match["rank"]["gapple"][$p2->getName()] = $p1->getName();
                                    PlayerManager::$playerstatus[$p1->getName()] = $id;
                                    PlayerManager::$playerstatus[$p2->getName()] = $id;
                                } else {
                                    if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rgapple4 = Server::getInstance()->getWorldManager()->getWorldByName("duel4")->getSafeSpawn();
                            if($rgapple4 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rgapple4);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                        $manager->sendDuelKit($p1, $id);
                                        $manager->sendDuelKit($p2, $id);
                                        unset(self::$rankqueue["gapple"][0]);
                                        unset(self::$rankqueue["gapple"][1]);
                                        if (!isset(self::$match["rank"]["gapple"])) {
                                            self::$match["rank"]["gapple"] = [];
                                        }
                                        self::$match["rank"]["gapple"][$p1->getName()] = $p2->getName();
                                        self::$match["rank"]["gapple"][$p2->getName()] = $p1->getName();
                                        PlayerManager::$playerstatus[$p1->getName()] = $id;
                                        PlayerManager::$playerstatus[$p2->getName()] = $id;
                                    } else {
                                         if (count(Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getPlayers()) == 0) {
                            $manager = new KitManager();
                             $rgapple5 = Server::getInstance()->getWorldManager()->getWorldByName("duel5")->getSafeSpawn();
                            if($rgapple5 === null){
                                return;
                            }
                            $pos = new Vector3((float)self::$posduel[0], (float)self::$posduel[1], (float)self::$posduel, $rgapple5);
                            $p1->teleport($pos);
                            $p2->teleport($pos);
                                            $manager->sendDuelKit($p1, $id);
                                            $manager->sendDuelKit($p2, $id);
                                            unset(self::$rankqueue["gapple"][0]);
                                            unset(self::$rankqueue["gapple"][1]);
                                            if (!isset(self::$match["rank"]["gapple"])) {
                                                self::$match["rank"]["gapple"] = [];
                                            }
                                            self::$match["rank"]["gapple"][$p1->getName()] = $p2->getName();
                                            self::$match["rank"]["gapple"][$p2->getName()] = $p1->getName();
                                            PlayerManager::$playerstatus[$p1->getName()] = $id;
                                            PlayerManager::$playerstatus[$p2->getName()] = $id;
                                        }
                                    }
                                }
                            }
                        }
                        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);
                    }

                }
        }
        ++self::$duelindex;
        try {
            WorldUtils::duplicateWorld("duel0", "dpduel" . self::$duelindex);
            WorldUtils::renameWorld("dpduel" . self::$duelindex, "duel" . self::$duelindex);
        }catch(\ErrorException $exception) {
        }
        $ids = [PlayerManager::BOXING_DUEL => "boxing", PlayerManager::FIST_DUEL_RANKED => "fist", PlayerManager::NODEBUFF_DUEL_RANKED => "nodebuff", PlayerManager::SUMO_DUEL => "sumo", PlayerManager::VOIDFIGHT_DUEL => "voidfight", PlayerManager::GAPPLE_DUEL_RANKED => "gapple"];
        $manager = new KitManager();
        $index = [];
        foreach (self::$rankqueue[$ids[$id]] as $key => $value) {
            $index[] = $key;
        }
        if (count($index) == 2) {
            $p1 = Server::getInstance()->getPlayerExact(self::$rankqueue[$ids[$id]][$index[0]]);
            $p2 = Server::getInstance()->getPlayerExact(self::$rankqueue[$ids[$id]][$index[1]]);
            if ($p1->isOnline() and $p2->isOnline()) {
                if ($id == PlayerManager::VOIDFIGHT_DUEL) {
                    ArenaResetter::reset("voidfight");
                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("voidfight" . ArenaResetter::$index["voidfight"])->getSafeSpawn());
                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("voidfight" . ArenaResetter::$index["voidfight"])->getSafeSpawn());
                    $p1->teleport(new Vector3(self::$posduel[0][0], self::$posduel[0][1], self::$posduel[0][2]));
                    $p2->teleport(new Vector3(self::$posduel[1][0], self::$posduel[1][1], self::$posduel[1][2]));
                    $manager->sendDuelKit($p1, $id);
                    $manager->sendDuelKit($p2, $id);
                    unset(self::$rankqueue[$ids[$id]][$index[0]]);
                    unset(self::$rankqueue[$ids[$id]][$index[1]]);
                    if (!isset(self::$match["rank"][$ids[$id]])) {
                        self::$match["rank"][$ids[$id]] = [];
                    }
                } else {
                    Server::getInstance()->getWorldManager()->loadWorld("duel" . self::$duelindex);
                    $p1->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel" . self::$duelindex)->getSafeSpawn());
                    $p2->teleport(Server::getInstance()->getWorldManager()->getWorldByName("duel" . self::$duelindex)->getSafeSpawn());
                    $p1->teleport(new Vector3(self::$posduel[0][0], self::$posduel[0][1], self::$posduel[0][2]));
                    $p2->teleport(new Vector3(self::$posduel[1][0], self::$posduel[1][1], self::$posduel[1][2]));
                    $manager->sendDuelKit($p1, $id);
                    $manager->sendDuelKit($p2, $id);
                    unset(self::$rankqueue[$ids[$id]][$index[0]]);
                    unset(self::$rankqueue[$ids[$id]][$index[1]]);
                    if (!isset(self::$match["rank"][$ids[$id]])) {
                        self::$match["rank"][$ids[$id]] = [];
                    }
                }
                self::$match["rank"][$ids[$id]][$p1->getName()] = $p2->getName();
                self::$match["rank"][$ids[$id]][$p2->getName()] = $p1->getName();
                PlayerManager::$playerstatus[$p1->getName()] = $id;
                PlayerManager::$playerstatus[$p2->getName()] = $id;
                Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelTask($p1, $p2), 20);
            }
        }
    }

    public static function unsetQueue(Player $player)
    {
        foreach (self::$unrankqueue as $indeks => $unranks) {
            foreach ($unranks as $index => $unrank) {
                if ($unrank == $player->getName()) {
                    unset(self::$unrankqueue[$indeks][$index]);
                }
            }
        }
        foreach (self::$rankqueue as $indeks => $ranks) {
            foreach ($ranks as $index => $rank) {
                if ($rank == $player->getName()) {
                    unset(self::$rankqueue[$indeks][$index]);
                }
            }
        }
    }

    public static function unsetMatch(Player $player)
    {
        foreach (self::$match as $index => $matchs) {
            foreach ($matchs as $indek => $match) {
                foreach ($match as $indeks => $matches) {
                    if ($indeks == $player->getName()) {
                        unset(self::$match[$index][$indek][$indeks]);
                    }
                    if ($matches == $player->getName()) {
                        unset(self::$match[$index][$indek][$matches]);
                    }
                }
            }
        }
    }

    public static function addrankQueue(Player $player, int $queueid)
    {
        $item =  new ItemFactory();
        switch ($queueid) {
            case PlayerManager::NODEBUFF_DUEL_RANKED:
                self::$rankqueue["nodebuff"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$rankqueue["nodebuff"]) >= 2) {
                    self::startrankGame($queueid);
                }
                break;
            case PlayerManager::FIST_DUEL_RANKED:
                self::$rankqueue["fist"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$rankqueue["fist"]) >= 2) {
                    self::startrankGame($queueid);
                }
                break;
            case PlayerManager::RESISTANCE_DUEL:
                self::$rankqueue["resistance"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$rankqueue["resistance"]) >= 2) {
                    self::startrankGame($queueid);
                }
                break;
            case PlayerManager::VOIDFIGHT_DUEL:
                self::$rankqueue["voidfight"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$rankqueue["voidfight"]) >= 2) {
                    self::startrankGame($queueid);
                }
                break;
            case PlayerManager::SUMO_DUEL:
                self::$rankqueue["sumo"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$rankqueue["sumo"]) >= 2) {
                    self::startrankGame($queueid);
                }
                break;
            case PlayerManager::BOXING_DUEL:
                self::$rankqueue["boxing"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$rankqueue["boxing"]) >= 2) {
                    self::startrankGame($queueid);
                }
                break;
            case PlayerManager::GAPPLE_DUEL_RANKED:
                self::$rankqueue["gapple"][] = $player->getName();
                $player->getInventory()->clearAll();
                $player->getInventory()->setItem(8, $item->get(ItemIds::REDSTONE, 0, 1)->setCustomName("Leave Queue"));
                if (count(self::$rankqueue["gapple"]) >= 2) {
                    self::startrankGame($queueid);
                }
                break;
        }
    }

    public static function isRankDuel(Player $player): bool{
        foreach (self::$match["rank"] as $index => $match){
            foreach ($match as $indeks => $matchs) {
                if($matchs == $player->getName() or $indeks == $player->getName() ){
                    return true;
                }
            }
        }
        return false;
    }

    public static function isMatch(Player $player): bool{
        foreach (self::$match as $index => $matchs){
            foreach ($matchs as $indeks => $match){
                foreach($match as $indekss => $matches) {
                    if ($matches == $player->getName() or $indekss == $player->getName()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /*public static function isCombo(Player $player): bool{
         foreach (self::$match as $matchs){
             foreach ($matchs["combo"] as $index => $match){
                 if($index == $player->getName() or $match == $player->getName()){
                     return true;
                 }
             }
         }
        return true;
    }*/

public static function nodebuffbotduel(Player $player, int $id){
        ++self::$duelindex;
    try {
        WorldUtils::duplicateWorld("easybot1", "easybot2" . self::$duelindex);
        WorldUtils::renameWorld("easybot2" . self::$duelindex, "easybot" . self::$duelindex);
    }catch(\ErrorException $exception){
    }
        Server::getInstance()->getWorldManager()->loadWorld("easybot" . self::$duelindex);
        $location = new Location(self::$posduel[0][0], self::$posduel[0][1], self::$posduel[0][2], Server::getInstance()->getWorldManager()->getWorldByName("easybot" . self::$duelindex), $player->getLocation()->getYaw(), $player->getLocation()->getPitch());
        $player->teleport($location);
        KitManager::sendDuelKit($player, PlayerManager::NODEBUFF_DUEL);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelBotTask($player, $id), 20);

}
 public static function fistbotduel(Player $player, int $id){
        ++self::$duelindex;
    try {
        WorldUtils::duplicateWorld("easybot1", "easybot2" . self::$duelindex);
        WorldUtils::renameWorld("easybot2" . self::$duelindex, "easybot" . self::$duelindex);
    }catch(\ErrorException $exception){
    }
        Server::getInstance()->getWorldManager()->loadWorld("easybot" . self::$duelindex);
        $location = new Location(self::$posduel[0][0], self::$posduel[0][1], self::$posduel[0][2], Server::getInstance()->getWorldManager()->getWorldByName("easybot" . self::$duelindex), $player->getLocation()->getYaw(), $player->getLocation()->getPitch());
        $player->teleport($location);
        KitManager::sendDuelKit($player, PlayerManager::FIST_DUEL);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DuelBotTask($player, $id), 20);

}   

public static function isVoidFight(Player $player)
{
    if(isset(self::$match["voidfight"])) {
        foreach (self::$match["voidfight"] as $index => $match) {
            return $index == $player->getName() or $match == $player->getName();
        }
    }
    return false;
}

    public static function isQueue(Player $player)
    {
        foreach (self::$unrankqueue as $indeks => $unranks) {
            foreach ($unranks as $index => $unrank) {
                if ($unrank == $player->getName()) {
                    return true;
                }
            }
        }
        foreach (self::$rankqueue as $indeks => $ranks) {
            foreach ($ranks as $index => $rank) {
                if ($rank == $player->getName()) {
                    return true;
                }
            }
        }
        return false;
    }
}
