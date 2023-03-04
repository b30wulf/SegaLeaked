<?php

declare(strict_types=1);

namespace EpicPearl\SpekledFrog;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Location;
use pocketmine\item\EnderPearl;
use pocketmine\player\Player;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\world\World;
use pocketmine\item\ItemIds;
use EpicPearl\SpekledFrog\EPearl;
use EpicPearl\SpekledFrog\task\PearlCooldown;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntityDataHelper;


class Main extends PluginBase implements Listener{
    
    public static $pcooldown = [];
    const prefix = "§l§cSE§fGA §e>";
    
    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
               EntityFactory::getInstance()->register(Epearl::class, function(World $world, CompoundTag $nbt) : Epearl {
            return new Epearl(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["EnderpearlEntity"]);
        
        $this->getServer()->getLogger()->info("ORIGINALY CREATED BY SPEKLEDFROG 100%");
        
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        /*  if ($packet instanceof InventoryTransactionPacket) {
        if ($packet->trData->getTypeId() == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
        }
        }*/
        if ($packet instanceof LevelSoundEventPacket and $packet->sound == 42) {
            if ($player === null)
                return;
            $item = $player->getInventory()->getItemInHand();
            if ($item instanceof EnderPearl) {
                if ($player->getWorld()->getFolderName() == "nodebuff") {
                    if (!isset(self::$pcooldown[$player->getName()])) {
                        if ($item->getId() == ItemIds::ENDER_PEARL) {
                            $entity = new EPearl(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld()), null, $player);
                            $entity->spawnToAll();
                            $player->sendMessage("§cEnderPearl is now on §a15 §cseconds Cooldown.");
                            Main::$pcooldown[$player->getName()] = $player->getName();
                            $this->getScheduler()->scheduleDelayedTask(new PearlCooldown($this, $player), 20 * 15);
                            $sound = new EndermanTeleportSound();
                            $player->broadcastSound($sound);
                            $thrown = true;
                            if ($thrown) {
                                $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
                            }
                            return;

                        } else {
                            if (isset(self::$pcooldown[$player->getName()])) {
                                $player->sendMessage("§bEnderPearl is currently on Cooldown.");
                                return;
                            }
                        }
                    }
                } else {
                    if ($player->getWorld()->getFolderName() == "build") {
                        $entity = new EPearl(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld()), null, $player);
                        $entity->spawnToAll();
                        $sound = new EndermanTeleportSound();
                        $player->broadcastSound($sound);
                        $thrown = true;
                    if ($thrown) {
                        $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
                    }
                    return;
                    }

                }

            }
        }
    }
        

    
    public function onEpearl(PlayerItemUseEvent $event){
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $throw = false;
        
        if ($item->getId() == ItemIds::ENDER_PEARL) {
            $event->cancel();
        if ($player->getWorld()->getFolderName() == "nodebuff") {
            if (!isset(self::$pcooldown[$player->getName()])) {               
                    $event->cancel();
                    $entity = new EPearl(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld()), null, $player);
                    $entity->spawnToAll();
                    $player->sendMessage("§cEnderPearl is now on §a15 §cseconds Cooldown.");
                    Main::$pcooldown[$player->getName()] = $player->getName();
                    $this->getScheduler()->scheduleDelayedTask(new PearlCooldown($this, $player), 20 * 15);
                    $sound = new EndermanTeleportSound();
                    $player->broadcastSound($sound);
                    $thrown = true;
                    if ($thrown) {
                        $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
                    }
                    return;

                } else {
                    if (isset(self::$pcooldown[$player->getName()])) {
                        if ($item instanceof EnderPearl) {
                            $player->sendMessage("§bEnderPearl is currently on Cooldown.");
                            $event->cancel();
                            // return;
                        }
                    }
                }
            } else {
                if ($player->getWorld()->getFolderName() == "build") {
                    $entity = new EPearl(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld()), null, $player);
                    $entity->spawnToAll();
                    $sound = new EndermanTeleportSound();
                    $player->broadcastSound($sound);
                    $thrown = true;
                if ($thrown) {
                    $player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
                }
                return;
                }

            }
        } 
            }
     public function onQuitSheeshh(PlayerQuitEvent $event){
         $player = $event->getPlayer();
         unset(self::$pcooldown[$player->getName()]);
     }
}
