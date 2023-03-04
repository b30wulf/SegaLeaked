<?php

namespace SegaCore\Core;

use pocketmine\block\Bed;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\ItemDespawnEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\SplashPotion;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\EndermanTeleportSound;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\arena\KitManager;
use SegaCore\Core\database\Database;
use SegaCore\Core\database\DatabaseControler;
use SegaCore\Core\game\Game;
use SegaCore\Core\player\CorePlayer;
use SegaCore\Core\task\NametagTask;
use pocketmine\network\mcpe\protocol\EmotePacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use SegaCore\Core\task\RespawnTask;
use SegaCore\Core\task\DuelRespawnTask;
use SegaCore\Core\task\Lobby;
use SegaCore\Core\task\ScoreboardTask;
use SegaCore\Core\task\BlockTask;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\utils\Filesystem;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\item\VanillaItems;
use SegaCore\Core\PlayerManager;
use pocketmine\utils\Config;
use SpekledFrog\KillStreak\KillStreak;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FileSystemIterator;
use pocketmine\utils\FileSytemIterator;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;

class EventListener implements Listener
{

    private $delay = [];
    private static $clicks = [];
    public static $device = [];
    public static $control = [];
    public $plugin;
    private $lastchat = [];
    private $chatdelay = [];
    private $toggle1;
    public static $autogg = [];
    public static $cpspopup = [];
    public static $movementsession = [];
    const prefix = "§l§cSE§fGA";


    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
       /* $name = $player->getName();
        $ip = $player->getNetworkSession()->getIp();
		$ex = $player->getNetworkSession()->getPlayerInfo()->getExtraData();
		$d = file_get_contents(Main::getInstance()->getDataFolder()."data.json");
		$data = [];
		if($d !== false){
			$data = json_decode($d, true);
		}
		if(isset($data["NEW"][$name])){
			if($data["NEW"][$name]){
				if(count($data["PFUI"][$name]) > 1){
					$have = false;
					foreach($data["PFUI"][$name] as $id => $data){
						if($data == hash("fnv164", $ip)){
							$have = true;
						}
					}
					if(!$have){
						$data["PFUI"][$name][count($data["PFUI"][$name])+1] = hash("fnv164", $ip);
					}
				}
				if(count($data["IP"][hash("fnv164", $ip)]) > 1){
					$have = false;
					foreach($data["IP"][hash("fnv164", $ip)] as $id => $data){
						if($data == $name){
							$have = true;
						}
					}
					if(!$have){
						$data["IP"][hash("fnv164", $ip)][count($data["IP"][hash("fnv164", $ip)]+1)] = $name;
					}
				}
				if(count($data["DID"][$ex["DeviceId"]]) > 1){
					$have = false;
					foreach($data["DID"][$ex["DeviceId"]] as $id => $data){
						if($data == $name){
							$have = true;
						}
					}
					if(!$have){
						$data["DID"][$ex["DeviceId"]][count($data["DID"][$ex["DeviceId"]])+1] = $name;
					}
				}
				if(count($data["PFUI0"][$name]) > 1){
					$have = false;
					foreach($data["PFUI0"][$name] as $id => $data){
						if($data == $ex["DeviceId"]){
							$have = true;
						}
					}
					if(!$have){
						$data["PFUI0"][$name][count($data["PFUI0"][$name]+1)] = $ex["DeviceId"];
					}
				}
			}else{
				$data["PFUI"][$name][] = hash("fnv164", $ip);
				$data["IP"][hash("fnv164", $ip)][] = $name;
				$data["DID"][$ex["DeviceId"]][] = $name;
				$data["PFUI0"][$name][] = $ex["DeviceId"];
			}
			if(!$data["NEW"][$name]) $data["NEW"][$name] = true;
		}else{
			$data["PFUI"][$name][] = hash("fnv164", $ip);
			$data["IP"][hash("fnv164", $ip)][] = $name;
			$data["DID"][$ex["DeviceId"]][] = $name;
			$data["PFUI0"][$name][] = $ex["DeviceId"];
			$data["NEW"][$name] = true;
		}
		file_put_contents(Main::getInstance()->getDataFolder()."data.json", json_encode($data));*/
            $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        $player->getInventory()->clearAll();
        self::sendItem($player);
        $event->setJoinMessage(TextFormat::GRAY . "[" . TextFormat::GREEN . "+" . TextFormat::GRAY . "]" . TextFormat::GREEN . " " . $player->getName());
        //Server::getInstance()->broadcastMessage(TextFormat::GRAY . "[" . TextFormat::GREEN . "+" . TextFormat::GRAY . "]" . TextFormat::GREEN . " " . $player->getName());
        PlayerManager::$playerstatus[$event->getPlayer()->getName()] = PlayerManager::LOBBY;
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreboardTask($event->getPlayer(), $player, $this), 20);
    }

    public function onAttack(EntityDamageByEntityEvent $event)
    {

        $manager = new KitManager();
        $player = $event->getEntity();
        $killer = $event->getDamager();
        $distance = $killer->getPosition()->distance($player->getPosition());
        if ($player instanceof Player and $killer instanceof Player) {
            if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby() or $killer->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                $event->cancel();
            }
            if($player->getWorld()->getFolderName() == "build"){
            if($killer->getWorld()->getFolderName() == "build"){
               // PlayerManager::setTimer($player, $killer);
               // Main::getInstance()->antiInterruptTask($player, $killer, $this);
                if (!isset(PlayerManager::$damager[$player->getName()]) and !isset(PlayerManager::$damager[$killer->getName()]) and !$event->isCancelled()) {
                PlayerManager::setEnemy($player, $killer);
                PlayerManager::setTimer($player, $killer);
                $player->sendMessage(self::prefix . " §l§e» §r§aYou are Now in Combat");
                $killer->sendMessage(self::prefix . " §l§e» §r§aYou are Now in Combat");
                  Main::getInstance()->antiInterruptTask($player, $killer, $this);
                } else {
                    PlayerManager::setTimer($player, $killer);

                }
}
            }
            if($player->getWorld()->getFolderName() == "Battle"){
            if($killer->getWorld()->getFolderName() == "Battle"){
               // PlayerManager::setTimer($player, $killer);
               // Main::getInstance()->antiInterruptTask($player, $killer, $this);
                if (!isset(PlayerManager::$damager[$player->getName()]) and !isset(PlayerManager::$damager[$killer->getName()]) and !$event->isCancelled()) {
                PlayerManager::setEnemy($player, $killer);
                PlayerManager::setTimer($player, $killer);
                $player->sendMessage(self::prefix . " §l§e» §r§aYou are Now in Combat");
                $killer->sendMessage(self::prefix . " §l§e» §r§aYou are Now in Combat");
                  Main::getInstance()->antiInterruptTask($player, $killer, $this);
                } else {
                    PlayerManager::setTimer($player, $killer);

                }
}
            }
            if($player->getWorld()->getFolderName() == "sumo" || $player->getWorld()->getFolderName() == "fist" || $player->getWorld()->getFolderName() == "gapple" || $player->getWorld()->getFolderName() == "nodebuff" || $player->getWorld()->getFolderName() == "combo" || $player->getWorld()->getFolderName() == "resistance"){
            if($killer->getWorld()->getFolderName() == "sumo" || $killer->getWorld()->getFolderName() == "fist" || $killer->getWorld()->getFolderName() == "gapple" || $killer->getWorld()->getFolderName() == "nodebuff" || $killer->getWorld()->getFolderName() == "combo" || $killer->getWorld()->getFolderName() == "resistance"){
            if (!isset(PlayerManager::$damager[$player->getName()]) and !isset(PlayerManager::$damager[$killer->getName()]) and !$event->isCancelled()) {
                PlayerManager::setEnemy($player, $killer);
                PlayerManager::setTimer($player, $killer);
                if (is_null($killer) || is_null($player)) return;
                $player->sendMessage(self::prefix . " §l§e» §r§aYou are Now in Combat With§f " . $killer->getDisplayName());
                $killer->sendMessage(self::prefix . " §l§e» §r§aYou are Now in Combat With§f " . $player->getDisplayName());
                Main::getInstance()->antiInterruptTask($player, $killer, $this);
                foreach(Server::getInstance()->getOnlinePlayers() as $pl){
                    if($pl !== $killer){
                        $player->hidePlayer($pl);
                      }
                    if($pl !== $player){
                        $killer->hidePlayer($pl);
                    }            
                }
            } elseif (isset(PlayerManager::$damager[$player->getName()]) and !isset(PlayerManager::$damager[$killer->getName()])) {
                $event->cancel();
                $killer->sendMessage(self::prefix . " §l§e» §r§cInterrupting is not allowed!");
            } elseif (!isset(PlayerManager::$damager[$player->getName()]) and isset(PlayerManager::$damager[$killer->getName()])) {
                $event->cancel();
                $killer->sendMessage(self::prefix . " §l§e» §r§cYour enemy is " . PlayerManager::$damager[$killer->getName()]);
            } elseif (isset(PlayerManager::$damager[$player->getName()])) {
                if ($killer->getName() !== PlayerManager::$damager[$player->getName()]) {
                    $event->cancel();
                    $killer->sendMessage(self::prefix . " §l§e» §r§cInterrupting is not allowed!");
                } else {
                    PlayerManager::setTimer($player, $killer);
                }
            }
            }
            }
            if ($player->getHealth() <= $event->getFinalDamage()) {
                if (!Arena::isMatch($player) and !Arena::isMatch($killer)) {
                    $event->cancel();
                    self::teleportLobby($player);
                    $worldname = $killer->getWorld()->getFolderName();
                    $finalhealth = $killer->getHealth();
                    $weapon = $killer->getInventory()->getItemInHand()->getName();
                    $playername = $player->getDisplayName();
                    $killername = $killer->getDisplayName();
                    $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                    $potsA = 0;
                    $potsB = 0;
                    foreach ($player->getInventory()->getContents() as $pots) {
                        if ($pots instanceof SplashPotion) ++$potsA;
                    }
                    foreach ($killer->getInventory()->getContents() as $pots) {
                        if ($pots instanceof SplashPotion) ++$potsB;
                    }
                    if ($killer->getWorld()->getFolderName() == "nodebuff" or $killer->getWorld()->getFolderName() == "nodebuff-low" or $killer->getWorld()->getFolderName() == "nodebuff-java") {
                      if (is_null($killer) || is_null($player)) return;
                      
                        $dm = $player->getDisplayName() . " §6[" . $potsA . " Pots] §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer->getDisplayName() . " §6[" . $potsB . " Pots - " . $finalhealth . " HP]";
                    } else {
                        $dm = "§a" . $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§c " . $killer->getDisplayName() . " §b[§f" . $finalhealth . "§c HP§b]";
                    }
                    $killer->sendMessage(self::prefix . " §l§e» §r" . $dm);
                    $player->sendMessage(self::prefix . " §l§e» §r" . $dm);
                    KillStreak::getInstance()->getProvider()->resetKSPoints($player);
                    KillStreak::getInstance()->getProvider()->addKSPoints($killer, 1);
                    $oldstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($player);
                    $newstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($killer);
                    if($oldstreak >= 5){
                        Server::getInstance()->broadcastMessage($player->getName() . " loose " . $oldstreak . " Killstreak");
                    }
                    if(is_int($newstreak / 5)){
                        Server::getInstance()->broadcastMessage($killer->getName() . " is on " . $newstreak . " Killstreak");
                    }
                    $manager->sendKit($killer, PlayerManager::$playerstatus[$killer->getName()]);
                    if(isset(PlayerManager::$autoGG[$player->getName()])){
                        $player->chat("GG");
                    }
                    if(isset(PlayerManager::$arenasp[$player->getName()])){
                        if($killer->getWorld()->getFolderName() === "nodebuff"){
                           $manager->sendKit($player, PlayerManager::NODEBUFF_FFA);
                           $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("nodebuff")->getSafeSpawn());
                           PlayerManager::$playerstatus[$player->getName()] = PlayerManager::NODEBUFF_FFA;
                           $event->cancel();
                           PlayerManager::unsetTimer($player);
                           PlayerManager::unsetDamager($player);
                           PlayerManager::unsetTimer($killer);
                           PlayerManager::unsetDamager($killer);
                        }
                        if($killer->getWorld()->getFolderName() === "fist"){
                           $manager->sendKit($player, PlayerManager::FIST_FFA);
                           $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("fist")->getSafeSpawn());
                           PlayerManager::$playerstatus[$player->getName()] = PlayerManager::FIST_FFA;
                           $event->cancel();
                           PlayerManager::unsetTimer($player);
                           PlayerManager::unsetDamager($player);
                           PlayerManager::unsetTimer($killer);
                           PlayerManager::unsetDamager($killer);
                        }
                        if($killer->getWorld()->getFolderName() === "combo"){
                           $manager->sendKit($player, PlayerManager::COMBO_FFA);
                           $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("combo")->getSafeSpawn());
                           PlayerManager::$playerstatus[$player->getName()] = PlayerManager::COMBO_FFA;
                           $event->cancel();
                           PlayerManager::unsetTimer($player);
                           PlayerManager::unsetDamager($player);
                           PlayerManager::unsetTimer($killer);
                           PlayerManager::unsetDamager($killer);
                        }
                        if($killer->getWorld()->getFolderName() === "gapple"){
                           $manager->sendKit($player, PlayerManager::GAPPLE_FFA);
                           $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("gapple")->getSafeSpawn());
                           PlayerManager::$playerstatus[$player->getName()] = PlayerManager::GAPPLE_FFA;
                           $event->cancel();
                           PlayerManager::unsetTimer($player);
                           PlayerManager::unsetDamager($player);
                           PlayerManager::unsetTimer($killer);
                           PlayerManager::unsetDamager($killer);
                        }
                        if($killer->getWorld()->getFolderName() === "build"){
                           Utils::randomtp($player);
                           $event->cancel();
                           PlayerManager::unsetTimer($player);
                           PlayerManager::unsetDamager($player);
                           PlayerManager::unsetTimer($killer);
                           PlayerManager::unsetDamager($killer);
                        }
                        
                    }
                } else {
                    if(Arena::isVoidFight($player) and Arena::isVoidFight($player)){
                        if(isset(Game::$bed[$player->getName()]) and isset(Game::$bed[$killer->getName()])){
                            if(Game::$bed[$player->getName()]) {
                                $player->setGamemode(GameMode::SPECTATOR());
                                Main::getInstance()->getScheduler()->scheduleRepeatingTask(new RespawnTask($player), 20);
                                return;
                            } else {
                            }
                        }
                    }
                    if(Arena::isRankDuel($player) and Arena::isRankDuel($killer)){
                        $this->addEloToProperty($killer, mt_rand(10, 20));
                    }
                    $event->cancel();
                    if(PlayerManager::FIST_DUEL_UNRANKED){
                        $webHook = new Webhook("https://discord.com/api/webhooks/989474655410745366/awjy3n5UfKBUcSIvhHYnGa9WAA8RmAvRBiciefKe_-JlgjwmL7MZWcPef_1IO5mqcY5s");
                $embed = new Embed();
                $msg = new Message();
                $embed->setColor(16574595); // Yellow
                $embed->setTitle("Unranked fist");
                $embed->addField("Winner", "{$killer->getName()}");
                $embed->addField("Loser", "{$player->getName()}");
                $msg->addEmbed($embed);
                $webHook->send($msg);
                    }
                    //self::teleportLobby($player);
                    $player->setGamemode(GameMode::SPECTATOR());
                    $killer->sendTitle(TextFormat::GOLD . "VICTORY");
                    $killer->sendMessage(TextFormat::GREEN . "Winner: " . TextFormat::RESET . $killer->getName() . "\n" . TextFormat::RED . "Loser: " . TextFormat::RESET . $player->getName());
                    $player->sendMessage(TextFormat::GREEN . "Winner: " . TextFormat::RESET . $killer->getName() . "\n" . TextFormat::RED . "Loser: " . TextFormat::RESET . $player->getName());
                  // self::teleportLobby($killer);
                    Main::getInstance()->getScheduler()->scheduleDelayedTask(new DuelRespawnTask($this, $player), 20 * 3);
                    Main::getInstance()->getScheduler()->scheduleDelayedTask(new DuelRespawnTask($this, $killer), 20 * 3);
                    //Main::getInstance()->getScheduler()->scheduleDelayedTask(new Lobby($this, $player), 10 * 3);
                    //Main::getInstance()->getScheduler()->scheduleDelayedTask(new Lobby($this, $killer), 10 * 3);
                    $player->setGamemode(GameMode::SPECTATOR());
                    $killer->setHealth(20);
                    --Arena::$duelindex;
                    Arena::unsetMatch($player);
                    Arena::unsetMatch($killer);
                    $level = Server::getInstance()->getWorldManager()->getWorldByName($player->getWorld()->getFolderName());
                   /* if($level !== "duel1" || $level !== "lobby1" || $level !== "duel0"){
                    $worldName = $player->getWorld()->getFolderName();
                     if($worldName !== "lobby1"){
                    Server::getInstance()->getWorldManager()->unloadWorld(Server::getInstance()->getWorldManager()->getWorldByName($worldName));
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = Server::getInstance()->getDataPath() . "/worlds/" . $worldName, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $fileInfo) {
                if ($filePath = $fileInfo->getRealPath()) {
                    if ($fileInfo->isFile()) {
                        unlink($filePath);
                    } else {
                        rmdir($filePath);
                    }
                }
            }
                     
                 
            rmdir($worldPath);
                self::teleportLobby($player);
                       } 
                    }*/
                }
                if(isset(DatabaseControler::$kill[$killer->getName()]))
                ++DatabaseControler::$kill[$killer->getName()];
                if(isset(DatabaseControler::$death[$player->getName()]))
                ++DatabaseControler::$death[$player->getName()];
                LevelManager::addExp($killer, mt_rand(20, 50));
                DatabaseControler::$coins[$killer->getName()] += mt_rand(20, 50);
                Utils::anvilsound($killer);
            }
        } elseif ($killer instanceof Human and $player instanceof Player) {
            if ($player->getHealth() <= $event->getFinalDamage()) {
                $player->sendMessage("" . "\n" .  TextFormat::GREEN . "Winner: " . TextFormat::RESET . $killer->getName() . "\n" . TextFormat::RED . "Loser: " . TextFormat::RESET . $player->getName() . "\n" . "");
               self::teleportLobby($player);
                --Arena::$duelindex;
                if(!$player->getWorld()->getFolderName() == "easybot1"){
                $worldName = $player->getWorld()->getFolderName();
                    Server::getInstance()->getWorldManager()->unloadWorld(Server::getInstance()->getWorldManager()->getWorldByName($worldName));
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = Server::getInstance()->getDataPath() . "/worlds/" . $worldName, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $fileInfo) {
                if ($filePath = $fileInfo->getRealPath()) {
                    if ($fileInfo->isFile()) {
                        unlink($filePath);
                    } else {
                        rmdir($filePath);
                    }
                }
            }
            rmdir($worldPath);
                $event->cancel();
            }
               } 
        }
    }
    

    public static function sendItem(Player $player)
    {
        $player->getEffects()->clear();
        $player->setGamemode(GameMode::ADVENTURE());
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $item = new ItemFactory();
        if (Arena::isQueue($player)) {
            $player->getInventory()->setItem(8, VanillaItems::REDSTONE_DUST()->setCustomName("§l§e»§r§cLeave Queue"));
        } else {
            $player->getInventory()->setItem(0, VanillaItems::DIAMOND_SWORD()->setCustomName("§l§e»§cPlay §fFFA"));
            //$player->getInventory()->setItem(1, VanillaItems::IRON_SWORD()->setCustomName("§l§e»§cDuels"));
            /*$player->getInventory()->setItem(2, VanillaItems::GOLDEN_AXE()->setCustomName("§l§e»§cSelf §fPractice"));*/
            $player->getInventory()->setItem(4, VanillaItems::BOOK()->setCustomName("§l§e»§cYour §fStats"));
            $player->getInventory()->setItem(7, VanillaBlocks::OAK_SAPLING()->asItem()->setCustomName("§l§e»§cCosmetic"));
            $player->getInventory()->setItem(8, VanillaItems::CLOCK()->setCustomName("§l§e»§cSettings"));
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $pearl = $event->getItem();
        $id = $event->getItem()->getId();
        switch ($id) {
            case ItemIds::DIAMOND_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        $form = new FormManager();
                        $form->ffaForm($player);
                }
                break;
            case ItemIds::IRON_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        $form = new FormManager();
                        $form->duelsForm($player);
                }
                break;
            case ItemIds::GOLD_AXE:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        $form = new FormManager();
                        $form->botForm($player);
                }
                break;
            case ItemIds::REDSTONE:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    Arena::unsetQueue($player);
                    $player->getInventory()->clearAll();
                    self::sendItem($player);
                }
                break;
            case ItemIds::SAPLING:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        $form = new FormManager();
                        $form->cosmeticshop($player);
                }
                break;
            case ItemIds::CLOCK:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        $form = new FormManager();
                        $form->setting($player);
                }
                break;
                 case ItemIds::BOOK:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                        $form = new FormManager();
                        $form->statsform($player);                  
                }
                break;
        }
             
    }

    

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
       // Server::getInstance()->broadcastMessage(TextFormat::GRAY . "[" . TextFormat::RED . "-" . TextFormat::GRAY . "]" . TextFormat::RED . " " . $player->getName());
        $event->setQuitMessage(TextFormat::GRAY . "[" . TextFormat::RED . "-" . TextFormat::GRAY . "]" . TextFormat::RED . " " . $player->getName());
        unset(self::$clicks[$event->getPlayer()->getName()]);
        $player->getInventory()->clearAll();
        if (isset(PlayerManager::$damager[$player->getName()])) {
            $damager = Server::getInstance()->getPlayerExact(PlayerManager::$damager[$event->getPlayer()->getName()]);
               if (!is_null($damager) and $damager->isConnected()){
                if ($damager->isOnline()){
                    ++DatabaseControler::$kill[$damager->getName()];
                    $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                   // Server::getInstance()->broadcastMessage(self::prefix . " §l§e» §r" . $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $damager->getName() . " §6[" . $damager->getHealth() . " HP]");
                    $damager->sendMessage(self::prefix . " §l§e» §r" . $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $damager->getName() . " §6[" . $damager->getHealth() . " HP]");
                    KillStreak::getInstance()->getProvider()->resetKSPoints($player);
                    KillStreak::getInstance()->getProvider()->addKSPoints($damager, 1);
                    $oldstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($player);
                    $newstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($damager);
                    if ($oldstreak >= 5) {
                        Server::getInstance()->broadcastMessage($player->getName() . " loose " . $oldstreak . " Killstreak");
                    }
                    if (is_int($newstreak / 5)) {
                        Server::getInstance()->broadcastMessage($damager->getName() . " is on " . $newstreak . " Killstreak");
                    }
                    PlayerManager::unsetTimer($player);
                    PlayerManager::unsetDamager($player);
                } else {
                if(isset(PlayerManager::$damager[$player->getName()]) && isset(DatabaseControler::$kill[PlayerManager::$damager[$player->getName()]])){
			++DatabaseControler::$kill[PlayerManager::$damager[$player->getName()]];
		    }
            }
            if(isset(DatabaseControler::$death[$player->getName()])){
		++DatabaseControler::$death[$player->getName()];
	    }
            LevelManager::addExp($damager, mt_rand(20, 50));
               }
        }
        $fistkit = KitManager::$kit["fist"][$player->getName()];
        $gapplekit = KitManager::$kit["gapple"][$player->getName()];
        $nodebuffkit = KitManager::$kit["nodebuff"][$player->getName()];
        $combokit = KitManager::$kit["combo"][$player->getName()];
        $builduhc = KitManager::$kit["builduhc"][$player->getName()];
        $voidfight = KitManager::$kit["voidfight"][$player->getName()];
        $blockin = KitManager::$kit["blockin"][$player->getName()];
        $playername = $player->getName();
        Database::getDatabase()->query("UPDATE playerkit SET gapplekit='$gapplekit' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET fistkit='$fistkit' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET nodebuffkit='$nodebuffkit' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET combokit='$combokit' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET builduhckit='$builduhc' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET voidfightkit='$voidfight' WHERE username='$playername'");
        Database::getDatabase()->query("UPDATE playerkit SET blockinkit='$blockin' WHERE username='$playername'");
        unset(LevelManager::$level[$player->getName()]);
        unset(PlayerManager::$playerstatus[$player->getName()]);
        unset($this->plugin->rank[$player->getName()]);
        if (Arena::isMatch($player)) {
            foreach (Arena::$match as $index => $matchs) {
                foreach ($matchs as $indeks => $match) {
                    if ($indeks == $player->getName()) {
                        $enemy = Server::getInstance()->getPlayerExact($match);
                        if ($enemy->isOnline()) {
                            $enemy->sendTitle(TextFormat::YELLOW . "VICTORY!");
                        }
                    }
                }
            }
        }
        
        Arena::unsetQueue($player);
        Arena::unsetMatch($player);
        DatabaseControler::setKill($player, DatabaseControler::$kill[$player->getName()]);
        DatabaseControler::setDeath($player, DatabaseControler::$death[$player->getName()]);
        DatabaseControler::setElo($player, DatabaseControler::$elo[$player->getName()]);
        DatabaseControler::setCoin($player, DatabaseControler::$coins[$player->getName()]);
        DatabaseControler::setCosmetic($player, DatabaseControler::$cosmetic[$player->getName()]);
        unset(DatabaseControler::$kill[$player->getName()], DatabaseControler::$death[$player->getName()], DatabaseControler::$elo[$player->getName()], DatabaseControler::$coins[$player->getName()],
            DatabaseControler::$cosmetic[$player->getName()]);
        unset(PlayerManager::$playerstatus[$player->getName()]);
    }
    public function addClick(Player $player)
    {
        if(!isset(self::$clicks[$player->getName()]) || empty(self::$clicks[$player->getName()])){
            self::$clicks[$player->getName()][] = microtime(true);
	}else{
	    array_unshift(self::$clicks[$player->getName()], microtime(true));
	    if (count(self::$clicks[$player->getName()]) >= 100) {
	        array_pop(self::$clicks[$player->getName()]);
	    }
	    if (isset(PlayerManager::$cps[$player->getName()])) {
	        $player->sendTip(TextFormat::RED . "CPS§f: " . TextFormat::RESET . self::getCps($player));
	    }
	}
    }

    public function onPacketReceive(DataPacketReceiveEvent $event)
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        //if(isset(PlayerManager::$autosprint[$player->getName()])){
        /*if ($event->getPacket()->pid() === PlayerAuthInputPacket::NETWORK_ID) {
        if($player->isSprinting() && $packet->hasFlag(PlayerAuthInputFlags::DOWN)){
							$player->setSprinting(false);
						}elseif(!$player->isSprinting() && $packet->hasFlag(PlayerAuthInputFlags::UP)){
							$player->setSprinting();
						}
        }*/
        if ($packet instanceof InventoryTransactionPacket) {
            if ($packet->trData->getTypeId() == InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                $this->addClick($event->getOrigin()->getPlayer());
                if($player === null) return;
                $clicks = self::getCps($player);
                 if($clicks > 18){
                     $player->sendTitle(TextFormat::RED . "Please lower your debounce!!");
                 }
                if($clicks > 20){
                     $player->sendTitle(TextFormat::RED . "YOU HAVE 20 CPS!");
                 }
                if($clicks >= 30){
                     $player->kick("You have been kicked from the Network for having 30 Cps");
                     Server::getInstance()->broadcastMessage(TextFormat::GREEN . $player->getName() .  " has been kicked for having 30 CPS");
                 }
            }
        }
        if ($packet instanceof LevelSoundEventPacket and $packet->sound == 42) {
            $this->addClick($player);
            if($player === null) return;
                $clicks = self::getCps($player);
                 if($clicks > 18){
                     $player->sendTitle(TextFormat::RED . "Please lower your debounce!!");
                 }
                if($clicks > 20){
                     $player->sendTitle(TextFormat::RED . "YOU HAVE 20 CPS!");
                 }
                if($clicks >= 30){
                     $player->kick("You have been kicked from the Network for having 30 Cps");
                     Server::getInstance()->broadcastMessage(TextFormat::GREEN . $player->getName() .  " has been kicked for having 30 CPS");
                 }
        if ($event->getPacket()->pid() === AnimatePacket::NETWORK_ID) {
            $event->getOrigin()->getPlayer()->getServer()->broadcastPackets($event->getOrigin()->getPlayer()->getViewers(), [$event->getPacket()]);
            $event->cancel();
    }
        }
    }
    /**
     * @param DataPacketSendEvent $ev
     * @return void
     */
    public function onDataPacketSend(DataPacketSendEvent $event): void
    {
        foreach($event->getPackets() as $packet)
        {
            /** @var LevelSoundEventPacket $packet */
            if ($packet->pid() == LevelSoundEventPacket::NETWORK_ID)
            {
                if($packet->sound === LevelSoundEvent::ATTACK) $event->cancel();
                elseif($packet->sound === LevelSoundEvent::ATTACK_NODAMAGE) $event->cancel();
                elseif($packet->sound === LevelSoundEvent::ATTACK_STRONG) $event->cancel();
            }
            if($packet instanceof DisconnectPacket and $packet->message === "SEGA is whitelisted"){
                $packet->message = "SEGA whitelisted please check our discord server for more information dsc.gg/segamc";
            }
        }
    }

    public static function getCps(Player $player, float $deltaTime = 1.0, int $roundPrecision = 1): float
    {
        if (empty(self::$clicks[$player->getName()])) {
            return 0.0;
        }
        $mt = microtime(true);
        return round(count(array_filter(self::$clicks[$player->getName()], static function (float $t) use ($deltaTime, $mt): bool {
                return ($mt - $t) <= $deltaTime;
            })) / $deltaTime, $roundPrecision);
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $cause = $event->getCause();
     $entity = $event->getEntity();
        if ($event->getCause() == EntityDamageEvent::CAUSE_FALL) {
         $event->cancel();
        }
        if($event instanceof EntityDamageByEntityEvent and $entity instanceof Player){
            $damager = $event->getDamager();
            $distance = $damager->getPosition()->distance($entity->getPosition());
            $maxreach = 4.5;
            if($distance > $maxreach){
                foreach(Server::getInstance()->getOnlinePlayers() as $players){
                if($players->hasPermission("sega.antireach")){
                    //$players->sendMessage($damager->getName() . " is using " . $distance . " distance of reach");
            }
            }
            }
            
    }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $manager = new KitManager();
        if(isset(PlayerManager::$arenasp[$player->getName()])){
             self::$movementsession[$player->getName()] = 120;
        if ($player->getPosition()->getY() <= 1) {
            if ($player->getWorld()->getFolderName() == "sumo" || $player->getWorld()->getFolderName() == "build" || $player->getWorld()->getFolderName() == "lobby1" || $player->getWorld()->getFolderName() == "Battle") {
                if (isset(PlayerManager::$damager[$player->getName()])) {
                    $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                    $killer = Server::getInstance()->getPlayerExact(PlayerManager::$damager[$player->getName()]);
                    
                   if (is_null($killer) || is_null($player)) return;
                   
                    $player->sendMessage(self::prefix . " §l§e» §r" . $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer->getName() . " §6[" . "20" . " HP]");
                     $killer->sendMessage(self::prefix . " §l§e» §r" . $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer->getName() . " §6[" . "20" . " HP]");
                   KillStreak::getInstance()->getProvider()->resetKSPoints($player);
                   KillStreak::getInstance()->getProvider()->addKSPoints($killer, 1);
                   $oldstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($player);
                   $newstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($killer);
                    if($oldstreak >= 5){
                        Server::getInstance()->broadcastMessage($player->getName() . " loose " . $oldstreak . " Killstreak");
                    }
                    if(is_int($newstreak / 5)){
                        Server::getInstance()->broadcastMessage($killer->getName() . " is on " . $newstreak . " Killstreak");
                    }
                    if($player->getWorld()->getFolderName() == "sumo"){
                    $manager->sendKit($player, PlayerManager::SUMO_FFA);
                    $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumo")->getSafeSpawn());
                    PlayerManager::$playerstatus[$player->getName()] = PlayerManager::SUMO_FFA;
                    PlayerManager::unsetTimer($player);
                    PlayerManager::unsetDamager($player);
                    PlayerManager::unsetTimer($killer);
                    PlayerManager::unsetDamager($killer);
                    }
                    if($player->getWorld()->getFolderName() == "build"){
                    Utils::randomtp($player);
                    PlayerManager::$playerstatus[$killer->getName()] = PlayerManager::BUILD_FFA;
                    $manager->sendKit($killer, PlayerManager::BUILD_FFA);
                    PlayerManager::unsetTimer($player);
                    PlayerManager::unsetDamager($player);
                    PlayerManager::unsetTimer($killer);
                    PlayerManager::unsetDamager($killer);
                    }
                    if($player->getWorld()->getFolderName() == "Battle"){
                    Utils::battlepos($player);
                    PlayerManager::$playerstatus[$killer->getName()] = PlayerManager::BATTLE;
                    $manager->sendKit($killer, PlayerManager::BATTLE);
                    PlayerManager::unsetTimer($player);
                    PlayerManager::unsetDamager($player);
                    PlayerManager::unsetTimer($killer);
                    PlayerManager::unsetDamager($killer);
                    }
                   if ($player->isOnline() ||$killer->isOnline()) {
                       if(isset(PlayerManager::$damager[$player->getName()])){
                           $killer = Server::getInstance()->getPlayerExact(PlayerManager::$damager[$player->getName()]); 
                           if(isset(DatabaseControler::$kill[$killer->getName()])){
                               ++DatabaseControler::$kill[$killer->getName()];
                           }
                       }
                    } else {
                        if (isset(PlayerManager::$damager[$player->getName()])) {
                            ++DatabaseControler::$kill[PlayerManager::$damager[$player->getName()]];
                        }
                    }
                    ++DatabaseControler::$death[$player->getName()];
                    LevelManager::addExp($killer, mt_rand(20, 50));
            }
              if($player->getWorld()->getFolderName() == "sumo"){
              $manager->sendKit($player, PlayerManager::SUMO_FFA);
              $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName("sumo")->getSafeSpawn());
              }
              if($player->getWorld()->getFolderName() == "build"){
                 Utils::randomtp($player);              
               }
                if($player->getWorld()->getFolderName() === "Battle"){
                 Utils::battlepos($player);              
               }
              if($player->getWorld()->getFolderName() === "lobby1"){
              self::teleportLobby($player);
              
        }
    }
        }
        }
        if(!isset(PlayerManager::$arenasp[$player->getName()])){
        self::$movementsession[$player->getName()] = 120;
        if ($player->getPosition()->getY() <= 1) {
            if ($player->getWorld()->getFolderName() == "sumo" || $player->getWorld()->getFolderName() == "build" || $player->getWorld()->getFolderName() == "lobby1" || $player->getWorld()->getFolderName() == "Battle") {
                $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        self::sendItem($player);
                if (isset(PlayerManager::$damager[$player->getName()])) {
                    $messages = ["quickied", "railed", "ezed", "clapped", "given an L", "smashed", "botted", "utterly defeated", "swept off their feet", "sent to the heavens", "killed", "owned"];
                    $killer = Server::getInstance()->getPlayerExact(PlayerManager::$damager[$player->getName()]);
                                    if (is_null($killer) || is_null($player)) return;
				            $player->sendMessage(self::prefix . " §l§e» §r" . $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer->getName() . " §6[" . "20" . " HP]");
                    $killer->sendMessage(self::prefix . " §l§e» §r" . $player->getDisplayName() . " §7Was " . $messages[array_rand($messages)] . " §7By§b " . $killer->getName() . " §6[" . "20" . " HP]");
                   KillStreak::getInstance()->getProvider()->resetKSPoints($player);
                   KillStreak::getInstance()->getProvider()->addKSPoints($killer, 1);
                   $oldstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($player);
                   $newstreak = KillStreak::getInstance()->getProvider()->getPlayerKSPoints($killer);
                    if($oldstreak >= 5){
                        Server::getInstance()->broadcastMessage($player->getName() . " loose " . $oldstreak . " Killstreak");
                    }
                    if(is_int($newstreak / 5)){
                        Server::getInstance()->broadcastMessage($killer->getName() . " is on " . $newstreak . " Killstreak");
                    }
                    self::teleportLobby($player);
                    if ($killer->isOnline()) {
                        $killer = Server::getInstance()->getPlayerExact(PlayerManager::$damager[$player->getName()]);
                        ++DatabaseControler::$kill[$killer->getName()];
                    } else {
                        if (isset(PlayerManager::$damager[$player->getName()])) {
                            ++DatabaseControler::$kill[PlayerManager::$damager[$player->getName()]];
                        }
                    }
                    ++DatabaseControler::$death[$player->getName()];
                    LevelManager::addExp($killer, mt_rand(20, 50));
                    
            }
            $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        self::sendItem($player);
        }
    }
    }
    }
    
    public function onFreeze(PlayerMoveEvent $event){
        $player = $event->getplayer();
        if(isset(PlayerManager::$freeze[$player->getName()])){
            //$player->setImobile(true);
           $event->cancel();
        }
    }
    public static function teleportLobby(Player $player)
    {
        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::LOBBY;
        $player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        $player->getInventory()->clearAll();
        $player->setHealth(20);
        self::sendItem($player);
    }
    public static function lobby(Player $player)
    {
        PlayerManager::$playerstatus[$player->getName()] = PlayerManager::LOBBY;
        //$player->teleport(Server::getInstance()->getWorldManager()->getWorldByName(Main::getInstance()->getLobby())->getSafeSpawn());
        $player->getInventory()->clearAll();
        $player->setHealth(20);
        self::sendItem($player);
    }

     public function initJoin(Player $player){
            $rank = DatabaseControler::getRanks($player);
            $this->plugin->rank[$player->getName()] = $rank;
            LevelManager::$level[$player->getName()] = DatabaseControler::getLevel($player);
            $extradata = $player->getNetworkSession()->getPlayerInfo()->getExtraData();
            $os = ["Unknown", "Android", "iOS", "Mac", "FireOS", "GearVR", "HoloLens", "Win10", "Win32", "Dedicated", "Orbis", "PS4", "Nintendo", "Xbox One", "WinPhone", "Linux"];
            $control = ["Unknown", "Keyboard", "Touch", "Controller"];
            $cosmetic = unserialize(base64_decode(DatabaseControler::$cosmetic[$player->getName()]));
            self::$clicks[$player->getName()] = [];
            foreach ($cosmetic["equip"] as $key => $value) {
                if ($value !== "default") {
                    switch ($key) {
                        case "capes":
                            $oldSkin = $player->getSkin();
                            $skinmanager = new SkinManager();
                            $capeData = $skinmanager->createCape($value);
                            $setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
                            $player->setSkin($setCape);
                            $player->sendSkin();
                            break;
                        case "wings":
                            // TODO don't tw make wings on pm4
                            break;
                    }
                }
            }
            self::$control[$player->getName()] = $control[$extradata["CurrentInputMode"]] ?? $control[0];
            self::$device[$player->getName()] = $os[$extradata["DeviceOS"]] ?? $os[0];
            Main::getInstance()->getScheduler()->scheduleRepeatingTask(new NametagTask($player, $this), 1);
            $data = $player->getPlayerInfo()->getExtraData();
            $name = $data["ThirdPartyName"];
            if (isset($data["PersonaSkin"])){
                if ($data["PersonaSkin"]) {
                    if (!file_exists(Main::getInstance()->getDataFolder() . "saveskin")) {
                        mkdir(Main::getInstance()->getDataFolder() . "saveskin", 0777);
                    }
                    copy(Main::getInstance()->getDataFolder() . "steve.png", Main::getInstance()->getDataFolder() . "saveskin/$name.png");
                    return;
                }    
            }
            $saveSkin = new SkinManager();
            $saveSkin->saveSkin(base64_decode($data["SkinData"], true), $name);
        }

    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $ms = $event->getMessage();
        $ms = TextFormat::clean($ms);
        $event->setMessage($ms);
        if (isset(KitManager::$setupkit[$player->getName()])) {
            if (strtolower($event->getMessage()) == "y") {
                KitManager::saveKit($player, KitManager::$setupkit[$player->getName()]);
                $event->cancel();
                return;
            }
            if (strtolower($event->getMessage()) == "n") {
                $player->getInventory()->clearAll();
                self::sendItem($player);
                $event->cancel();
                return;
            }
            $player->sendMessage("Type y or n for save kits!");
        }
        if (isset($this->lastchat[$player->getName()])) {
            if ($event->getMessage() == $this->lastchat[$player->getName()]) {
              
            }
            $this->lastchat[$player->getName()] = $event->getMessage();
        } else {
            $this->lastchat[$player->getName()] = $event->getMessage();
        }
        if(isset($this->plugin->rank[$player->getName()])){
            if (strtoupper($this->plugin->rank[$player->getName()]) == "PLAYER") {
                if (isset($this->chatdelay[$player->getName()])) {
                    if ($this->chatdelay[$player->getName()] + 2 < time()) {
                        $this->chatdelay[$player->getName()] = time();
                    } else {
                        if (!$event->isCancelled()) {
                            $event->cancel();
                            $player->sendMessage(TextFormat::RED . "Please wait 2 Second to chat");
                        }
                    }
                } else {
                    $this->chatdelay[$player->getName()] = time();
                }
            } else {
                if (isset($this->chatdelay[$player->getName()])) {
                    if ($this->chatdelay[$player->getName()] + 1 < time()) {
                        $this->chatdelay[$player->getName()] = time();
                    } else {
                        if (!$event->isCancelled()) {
                            $event->cancel();
                            $player->sendMessage(TextFormat::RED . "Please wait 1 Second to chat");
                        }
                    }
                } else {
                    $this->chatdelay[$player->getName()] = time();
                }
            }
            if (strtoupper($this->plugin->rank[$player->getName()]) !== "PLAYER") {
                $event->setFormat(" " . RankManager::getRankFormat($this->plugin->rank[$event->getPlayer()->getName()]) . " " . $player->getName() . ": " . TextFormat::RESET . $event->getMessage());
            } else {
                $event->setFormat(TextFormat::GRAY . $player->getName() . ": " . TextFormat::RESET . $event->getMessage());
            }    
        }
        $p = Server::getInstance()->getOnlinePlayers();
        if(isset(PlayerManager::$sChat[$player->getName()]))
        {
            foreach($p as $pl)
            {
                if($pl->hasPermission("sega.staff"))
                {
                    $event->cancel();
                    $pl->sendMessage("§l§6StaffChat §e> §r§6" . $player->getName() . " §6: §r" . $event->getMessage());
                }
            }
        }
    }

    public function onExhaust(PlayerExhaustEvent $event)
    {
        $event->cancel();
    }

    public function onRegen(EntityRegainHealthEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if ($event->getRegainReason() == 4) {
                $event->cancel();
            }
        }
    }
    public function onPearlDamage(EntityDamageByChildEntityEvent $event){
        //$event->cancel();
    }

    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $event->setXpDropAmount(0);
        $event->setDrops([]);
        $event->setDeathMessage("");
    }

    public function onRespawn(PlayerRespawnEvent $event)
    {
        $player = $event->getPlayer();
        self::teleportLobby($player);
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
         $block = $event->getBlock();
        if($event->getBlock() instanceof Bed){
            if(substr($event->getBlock()->getPosition()->getWorld()->getFolderName(), 0, 9) == "voidfight") {
                $pos = ["x" => $event->getBlock()->getPosition()->getX(), "y" => $event->getBlock()->getPosition()->getY(), "z" => $event->getBlock()->getPosition()->getZ()];
                if ($event->getBlock()->getPosition()->getY() == 10) {
                    if (isset(Game::$team[$player->getName()])) {
                        if (Game::$team[$player->getName()] !== "blue") {
                            $enemy = Game::$enemy[$player->getName()];
                            if($enemy->isOnline()){
                                $enemy->sendTitle(TextFormat::BOLD . "BED DESTROYED", TextFormat::GREEN . "You no longer to respawn");
                                Game::$bed[$enemy->getName()] = false;
                            }
                            return;
                        } else {
                            $player->sendMessage(TextFormat::RED . "You cant break bed your team");
                            $event->cancel();
                        }
                        if(Game::$team[$player->getName()] !== "red"){
                            $enemy = Game::$enemy[$player->getName()];
                            if($enemy->isOnline()){
                                $enemy->sendTitle(TextFormat::BOLD . "BED DESTROYED", TextFormat::GREEN . "You no longer to respawn");
                                Game::$bed[$enemy->getName()] = false;
                            }
                            return;
                        } else {
                            $player->sendMessage(TextFormat::RED . "You cant break bed your team");
                            $event->cancel();
                        }
                    }
                }
                return;
            }  
        }
        $build = PlayerManager::$build[$player->getName()] ?? false;
        if(!$build) {
            $event->cancel();
            if($block->getId() === 24){
            $event->uncancel();
        }
        }
        if($build){
        if($block->getId() === 24){
            $event->uncancel();
        }
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
         $block = $event->getBlock();
        $build = PlayerManager::$build[$player->getName()] ?? false;
        if(!$build) {
            $event->cancel();
            if($block->getId() === 24){
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new BlockTask($this, $block), 20 * 10);
                $event->uncancel();
            }
        }
            if($build){
            if($block->getId() === 24){
          Main::getInstance()->getScheduler()->scheduleDelayedTask(new BlockTask($this, $block), 20 * 10);
                $event->uncancel();
        }
    }
    }

    public function addEloToProperty(Player $player, int $value)
    {
        $player->sendMessage("ELO CHANGES " . DatabaseControler::$elo[$player->getName()] . " +$value");
        DatabaseControler::$elo[$player->getName()] += $value;

    }
    public function addCoins(Player $player, int $value)
    {
        if (is_null($player)) return;
        $player->sendMessage("You have given coin ammount: " . DatabaseControler::$coins[$player->getName()] . " $value");
        DatabaseControler::$coins[$player->getName()] += $value;

    }

    public function onLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        $player->getInventory()->clearAll();
        self::teleportLobby($player);
        DatabaseControler::registerPlayer($player);
        $this->initJoin($player);
    }
    public function onChangeWorld(EntityTeleportEvent $event){
        $entity = $event->getEntity();
        if($event->getTo()->getWorld()->getFolderName() == Main::getInstance()->getLobby()){
            if($entity instanceof Player){
                PlayerManager::$playerstatus[$entity->getName()] = PlayerManager::LOBBY;
                if(isset($this->plugin->rank[$entity->getName()])){
                if(strtoupper($this->plugin->rank[$entity->getName()]) !== "PLAYER") {
               
                }
                }
            }
        }
    }

    public function onDrop(PlayerDropItemEvent $event){
        $event->cancel();
    }
    
    public function onHit(ProjectileHitBlockEvent $event){

        $projectile = $event->getEntity();

        $projectile->flagForDespawn();

        if($projectile instanceof \pocketmine\entity\projectile\SplashPotion){

            $player = $projectile->getOwningEntity();
            if(is_null($player)) return;

            if($player->isAlive()){
               // if(is_null($player)) return;
                

                $distance = $projectile->getPosition()->distance($player->getPosition()->asVector3());

                if($player instanceof Player and $distance <= 3){

                    $player->setHealth($player->getHealth() + 4);

                }

            }

        }

    }

    public function onUse(PlayerItemUseEvent $event){
        $player = $event->getPlayer();
        $id = $event->getItem()->getId();
        switch ($id) {
            case ItemIds::DIAMOND_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->ffaForm($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }

                }
                break;
            case ItemIds::IRON_SWORD:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->duelsForm($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                break;
            case ItemIds::GOLD_AXE:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->botForm($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                break;
            case ItemIds::REDSTONE:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    Arena::unsetQueue($player);
                    $player->getInventory()->clearAll();
                    self::sendItem($player);
                }
                break;
            case ItemIds::COMPASS:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->cosmeticshop($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                break;
            case ItemIds::RED_FLOWER:
                if ($player->getWorld()->getFolderName() == Main::getInstance()->getLobby()) {
                    if (!isset($this->delay[$player->getName()])) {
                        $form = new FormManager();
                        $form->usecosmeticform($player);
                        $this->delay[$player->getName()] = time();
                    } else {
                        if ($this->delay[$player->getName()] < time()) {
                            unset($this->delay[$player->getName()]);
                        }
                    }
                }
                if(isset(PlayerManager::$nopearl[$player->getName()])){
               if($event->getItem() instanceof EnderPearl){
                   $event->cancel();
               }
           }
        }
    }

    public function onPlayerCreation(PlayerCreationEvent $event){
        
    }
    public function onPadi(ItemDespawnEvent $event){
        $event->cancel();
    }
}
