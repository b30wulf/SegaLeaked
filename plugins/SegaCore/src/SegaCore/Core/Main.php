<?php

namespace SegaCore\Core;

/*---------------------------------
basic pocketmine uses
---------------------------------*/

use pocketmine\player\Player;
use pocketmine\world\World;
use pocketmine\world\WorldManager;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\scheduler\ClosureTask;

/*---------------------------------
core uses
---------------------------------*/

use SegaCore\Core\arena\Arena;
use SegaCore\Core\arena\ArenaResetter;
use SegaCore\Core\command\staff\BuildCommand;
use SegaCore\Core\command\HubCommand;
use SegaCore\Core\command\SpawnCommand;
use SegaCore\Core\command\perks\FlyCommand;
use SegaCore\Core\command\PingCommand;
use SegaCore\Core\command\RegionCommand;
use SegaCore\Core\command\FakeMSG;
use SegaCore\Core\database\Database;
use SegaCore\Core\database\DatabaseControler;
use SegaCore\Core\task\CombatTask;
use SegaCore\Core\task\WhitelistTask;
use SegaCore\Core\command\staff\AnnounceCommand;
use SegaCore\Core\command\staff\InfoCommand;
use SegaCore\Core\command\staff\CoinCommand;
use SegaCore\Core\command\staff\FreezeCommand;
use SegaCore\Core\command\staff\UnFreezeCommand;
use SegaCore\Core\command\staff\StaffChatCommand;
use SegaCore\Core\task\ToastBroadcasterTask;
use SegaCore\Core\task\DuelRespawnTask;
use SegaCore\Core\task\DayTask;
use SegaCore\Core\task\BlockTask;
use SegaCore\Core\arena\KitManager;
use SQLite3;
use SegaCore\Core\task\Lobby;
/*---------------------------------
pocketmine uses
---------------------------------*/

use pocketmine\block\tile\Spawnable;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\event\EventPriority;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntityDataHelper;
use pocketmine\network\mcpe\protocol\ToastRequestPacket;
use function array_diff;
use function scandir;
/*---------------------------------
library uses
---------------------------------*/

use SegaCore\libs\muqsit\simplepackethandler\interceptor\IPacketInterceptor;
use SegaCore\libs\muqsit\simplepackethandler\SimplePacketHandler;

class Main extends PluginBase{


    /** @var Main $instance */
    public static $instance;
    public $config;
    public $rank = [];
    private $sprint;
    public SQLite3 $database;
    public SQLite3 $database2;
    
    private IPacketInterceptor $handler;

	/** @phpstan-var \Closure(BlockActorDataPacket, NetworkSession): bool */
	private \Closure $handleBlockActorData;

	/** @phpstan-var \Closure(UpdateBlockPacket, NetworkSession): bool */
	private \Closure $handleUpdateBlock;
	private ?Player $lastPlayer = null;

	/**
	 * @var int[]
	 * @phpstan-var array<int, int>
	 */
	private array $oldBlocksFullId = [];

	/**
	 * @var CacheableNbt[]
	 * @phpstan-var array<int, CacheableNbt>
	 */
	private array $oldTilesSerializedCompound = [];


    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        if(!file_exists($this->getDataFolder()."data.json")){
			file_put_contents($this->getDataFolder()."data.json", "[]");
		}
		/*if(is_dir($this->getDataFolder() . "players/")) {
			$this->getLogger()->notice("UPDATING player datas");
			$data = [];
			$datas = array_diff(scandir($this->getDataFolder() . "players/"), ['..', '.']);
			foreach ($datas as $fileName) {
				$ip = str_replace(".txt", "", $fileName);
				$file = new Config($this->getDataFolder() . "players/" . $fileName);
				$names = $file->getAll(true);
				foreach($names as $name){
					$data["IP"][hash("fnv164", $ip)][] = $name;
                    $data["PFUI"][$name][] = hash("fnv164", $ip);
				}
				$data["NEW"][$name] = false;
				unlink($this->getDataFolder() . "players/" . $fileName);
			}
			file_put_contents($this->getDataFolder()."data.json", json_encode($data));
			@rmdir($this->getDataFolder() . "players");
		}*/
        $this->database =  new SQLite3(Main::getInstance()->getDataFolder() . "database.db");
        $this->database2 = new SQLite3(Main::getInstance()->getDataFolder() . "database2.db");
        $this->initDatabase();
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new DayTask($this), 1);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new WhitelistTask($this), 1);
        //$this->getScheduler()->scheduleRepeatingTask(new ToastBroadcasterTask($this), 20 * $this->getConfig()->getAll()["broadcast"]["delay"]);
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "capes");
        @mkdir($this->getDataFolder() . "players");
        $this->checkRequirement();
        $this->saveDefaultConfig();
        //$this->blockLagFix();
        $this->enderPearl();
        $this->getServer()->getNetwork()->setName("§l§cSE§fGA");
        $capes = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if(is_array($capes->get("standard_capes"))) {
            foreach ($capes->get("standard_capes") as $cape) {
                $this->saveResource("$cape.png");
            }
            $capes->set("standard_capes", "done");
            $capes->save();
        }
        
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getScheduler()->scheduleTask(new ClosureTask(function (): void {
        foreach(array_diff(scandir($this->getServer()->getDataPath() . "worlds"), [".."]) as $AllWorlds){
            if($this->getServer()->getWorldManager()->loadWorld($AllWorlds)){
                $this->getLogger()->info(">> $AllWorlds(LOADED)");
            }
        }
        $this->getLogger()->info("\n---------------------\n\n--- SEGA MC ---\n\n---------------------");
        }));
        $this->initCommand();
        $this->initDatabase();
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('kill'));
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('me'));
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('defaultgamemode'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('difficulty'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('spawnpoint'));
	    //$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('setworldspawn'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('msg'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('title'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('seed'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('particle'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('clear'));
	    $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('about'));
	    //$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('transferserver'));
        $this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('effect'));
        //$this->getServer()->getCommandMap()->unregister($this->getServer()->getCommandMap()->getCommand('stop'));
        Arena::$unrankqueue["nodebuff"] = [];
        Arena::$unrankqueue["fist"] = [];
        Arena::$unrankqueue["boxing"] = [];
        Arena::$unrankqueue["voidfight"] = [];
        Arena::$unrankqueue["gapple"] = [];
        Arena::$unrankqueue["sumo"] = [];
        Arena::$unrankqueue["resistance"] = [];
        Arena::$rankqueue["nodebuff"] = [];
        Arena::$rankqueue["fist"] = [];
        Arena::$rankqueue["boxing"] = [];
        Arena::$rankqueue["voidfight"] = [];
        Arena::$rankqueue["gapple"] = [];
        Arena::$rankqueue["sumo"] = [];
        Arena::$rankqueue["resistance"] = [];
        Arena::$match["rank"] = [];
        Arena::$match["unrank"] = [];
        KitManager::$kit["fist"] = [];
        KitManager::$kit["nodebuff"] = [];
        KitManager::$kit["combo"] = [];
        KitManager::$kit["builduhc"] = [];
        KitManager::$kit["voidfight"] = [];
        KitManager::$kit["blockin"] = [];
        KitManager::$kit["gapple"] = [];
       /* foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world){
            $world->setTime(1000);
            $world->stopTime();
        }*/
    }
    
    public function enderPearl(){
    	$this->getServer()->getPluginManager()->registerEvent(ProjectileHitEvent::class, static function (ProjectileHitEvent $event) : void{
            $projectile = $event->getEntity();
            $entity = $projectile->getOwningEntity();
            if ($projectile instanceof EnderPearl and $entity instanceof Player) {
                $vector = $event->getRayTraceResult()->getHitVector();
                (function() use($vector) : void{
                    $this->setPosition($vector);
                })->call($entity);
                $location = $entity->getLocation();
                $entity->getNetworkSession()->syncMovement($location, $location->yaw, $location->pitch);
                $projectile->setOwningEntity(null);
            }
        }, EventPriority::NORMAL, $this);
    }
    
    /*public function blockLagFix(){
    	$this->handler = SimplePacketHandler::createInterceptor($this, EventPriority::HIGHEST);

		$this->handleUpdateBlock = function(UpdateBlockPacket $packet, NetworkSession $target): bool{
			if($target->getPlayer() !== $this->lastPlayer){
				return true;
			}
			$blockHash = World::blockHash($packet->blockPosition->getX(), $packet->blockPosition->getY(), $packet->blockPosition->getZ());
			if(RuntimeBlockMapping::getInstance()->fromRuntimeId($packet->blockRuntimeId, RuntimeBlockMapping::getInstance()->getMappingProtocol($target->getProtocolId())) !== ($this->oldBlocksFullId[$blockHash] ?? null)){
				return true;
			}
			unset($this->oldBlocksFullId[$blockHash]);
			if(count($this->oldBlocksFullId) === 0){
				if(count($this->oldTilesSerializedCompound) === 0){
					$this->lastPlayer = null;
				}
				$this->handler->unregisterOutgoingInterceptor($this->handleUpdateBlock);
			}
			return false;
		};
		$this->handleBlockActorData = function(BlockActorDataPacket $packet, NetworkSession $target): bool{
			if($target->getPlayer() !== $this->lastPlayer){
				return true;
			}
			$blockHash = World::blockHash($packet->blockPosition->getX(), $packet->blockPosition->getY(), $packet->blockPosition->getZ());
			if($packet->nbt !== ($this->oldTilesSerializedCompound[$blockHash] ?? null)){
				return true;
			}
			unset($this->oldTilesSerializedCompound[$blockHash]);
			if(count($this->oldTilesSerializedCompound) === 0){
				if(count($this->oldTilesSerializedCompound) === 0){
					$this->lastPlayer = null;
				}
				$this->handler->unregisterOutgoingInterceptor($this->handleBlockActorData);
			}
			return false;
		};
		$this->getServer()->getPluginManager()->registerEvent(PlayerInteractEvent::class, function(PlayerInteractEvent $event): void{
			if($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK || !$event->getItem()->canBePlaced()){
				return;
			}
			$this->lastPlayer = $event->getPlayer();
			$clickedBlock = $event->getBlock();
			$replaceBlock = $clickedBlock->getSide($event->getFace());
			$this->oldBlocksFullId = [];
			$this->oldTilesSerializedCompound = [];
			foreach($clickedBlock->getAllSides() as $block){
				$pos = $block->getPosition();
				$posIndex = World::blockHash($pos->x, $pos->y, $pos->z);
				$this->oldBlocksFullId[$posIndex] = $block->getFullId();
				$tile = $pos->getWorld()->getTileAt($pos->x, $pos->y, $pos->z);
				if($tile instanceof Spawnable){
					$this->oldTilesSerializedCompound[$posIndex] = $tile->getSerializedSpawnCompound();
				}
			}
			foreach($replaceBlock->getAllSides() as $block){
				$pos = $block->getPosition();
				$posIndex = World::blockHash($pos->x, $pos->y, $pos->z);
				$this->oldBlocksFullId[$posIndex] = $block->getFullId();
				$tile = $pos->getWorld()->getTileAt($pos->x, $pos->y, $pos->z);
				if($tile instanceof Spawnable){
					$this->oldTilesSerializedCompound[$posIndex] = $tile->getSerializedSpawnCompound();
				}
			}
			$this->handler->interceptOutgoing($this->handleUpdateBlock);
			$this->handler->interceptOutgoing($this->handleBlockActorData);
		}, EventPriority::MONITOR, $this);
		$this->getServer()->getPluginManager()->registerEvent(BlockPlaceEvent::class, function(BlockPlaceEvent $event): void{
			$this->oldBlocksFullId = [];
			$this->oldTilesSerializedCompound = [];
			$this->lastPlayer = null;
			$this->handler->unregisterOutgoingInterceptor($this->handleUpdateBlock);
			$this->handler->unregisterOutgoingInterceptor($this->handleBlockActorData);
		}, EventPriority::MONITOR, $this, true);
	}*/

    /** @return Main */
    public static function getInstance(): Main{
        return self::$instance;
    }

    public function initDatabase(){
        DatabaseControler::init();
    }

    public function initCommand()
    {
        $this->getServer()->getCommandMap()->register("Hub", new HubCommand("lobby", "Teleport to hub"));
        $this->getServer()->getCommandMap()->register("Spawn", new SpawnCommand("spawn", "Teleport to hub"));
        $this->getServer()->getCommandMap()->register("Ping", new PingCommand("ping", "Calculates the latency"));
        $this->getServer()->getCommandMap()->register("Build", new BuildCommand("build", "Toggle build mode", $this));
        $this->getServer()->getCommandMap()->register("Anouncement", new AnnounceCommand("ano", "Make a announcement", $this));
        $this->getServer()->getCommandMap()->register("Information", new InfoCommand("pinfo", "Shows player info", $this));
        $this->getServer()->getCommandMap()->register("Givecoin", new CoinCommand("givecoin", "Adds coins to a player", $this));
        $this->getServer()->getCommandMap()->register("Fly", new FlyCommand("fly", "I believe i can fly", $this));
        //$this->getServer()->getCommandMap()->register("Region", new RegionCommand("region", "Choose a region", $this));
        $this->getServer()->getCommandMap()->register("Staffchat", new StaffChatCommand("sc", "Turn on/off staffchat", $this));
        $this->getServer()->getCommandMap()->register("chat", new FakeMSG("chat", "Sends a private message to the given player", $this));
       // $this->getServer()->getCommandMap()->register("Freeze", new FreezeCommand("freeze", "Freeze a player", $this));
       // $this->getServer()->getCommandMap()->register("Unfreeze", new UnFreezeCommand("unfreeze", "Unfreeze a player", $this));
    }

    public function getLobby(){
        return $this->config->get("lobbyname");
    }

    public function antiInterruptTask(Player $player, Player $player2, EventListener $listener){
        $this->getScheduler()->scheduleRepeatingTask(new CombatTask($player, $player2, $listener), 20);
    }
    public function DuelRespawnTask(Player $player, Player $player2, EventListener $listener){
        $this->getScheduler()->scheduleRepeatingTask(new DuelRespawnTask($player, $player2, $listener), 20);
    }
    public function onDisable(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            $playername = $player->getName();
            $nodebuffkit = KitManager::$kit["nodebuff"][$player->getName()];
            $fistkit = KitManager::$kit["fist"][$player->getName()];
            $gapplekit = KitManager::$kit["gapple"][$player->getName()];
            $combokit = KitManager::$kit["combo"][$player->getName()];
            $builduhc = KitManager::$kit["builduhc"][$player->getName()];
            $voidfight = KitManager::$kit["voidfight"][$player->getName()];
            $blockin = KitManager::$kit["blockin"][$player->getName()];
            Database::getDatabase()->query("UPDATE playerkit SET nodebuffkit='$nodebuffkit' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET fistkit='$fistkit' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET gapplekit='$gapplekit' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET combokit='$combokit' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET builduhckit='$builduhc' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET voidfightkit='$voidfight' WHERE username='$playername'");
            Database::getDatabase()->query("UPDATE playerkit SET blockinkit='$blockin' WHERE username='$playername'");
            DatabaseControler::setKill($player, DatabaseControler::$kill[$player->getName()]);
            DatabaseControler::setDeath($player, DatabaseControler::$death[$player->getName()]);
            DatabaseControler::setElo($player, DatabaseControler::$elo[$player->getName()]);
            DatabaseControler::setCoin($player, DatabaseControler::$coins[$player->getName()]);
            DatabaseControler::setCosmetic($player, DatabaseControler::$cosmetic[$player->getName()]);
            }
        /*try {
            ArenaResetter::$index["voidfight"] = 1;
            foreach (range(1, ArenaResetter::$index["voidfight"]) as $item) {
                ArenaResetter::removeWorld("voidfight" . $item);
            }

        } catch (\ErrorException|\UnexpectedValueException $exception) {

        }*/
    }

   /*public function onDisable(): void
    {}*/

    public function checkRequirement()
    {
        if (!file_exists(Main::getInstance()->getDataFolder() . "steve.png") || !file_exists(Main::getInstance()->getDataFolder() . "steve.json") || !file_exists(Main::getInstance()->getDataFolder() . "config.yml")) {
            if (file_exists(str_replace("config.yml", "", Main::getInstance()->getResources()["config.yml"]))) {
                $var = new SkinManager();
                $var->recurse_copy(str_replace("config.yml", "", Main::getInstance()->getResources()["config.yml"]), Main::getInstance()->getDataFolder());
            }
        }
    }
    public function sendToast(Player $player, string $title = "", string $subtitle = ""){
		$packet = ToastRequestPacket::create(
        	$title,
        	$subtitle
		);
		$player->getNetworkSession()->sendDataPacket($packet);
	}

	public function broadcastToast(string $title = "", string $subtitle = ""){
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$packet = ToastRequestPacket::create(
        		$title,
        		$subtitle
			);
			$player->getNetworkSession()->sendDataPacket($packet);
		}
	}
}
