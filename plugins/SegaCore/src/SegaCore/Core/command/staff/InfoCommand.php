<?php

namespace SegaCore\Core\command\staff;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use pocketmine\Server;
use pocketmine\player\OfflinePlayer;
use pocketmine\utils\Config;
use SegaCore\Core\PlayerManager;
use SegaCore\Core\Main;
use SegaCore\Core\database\DatabaseControler;


class InfoCommand extends Command{

    public function __construct(string $name, Translatable|string $description, Main $plugin)
    {
        parent::__construct($name, $description);
        $this->plugin = $plugin;
        parent::setAliases(["information"]);
        $this->setPermission('sega.info');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->hasPermission("sega.info")){
        if(count($args) < 1){
            $sender->sendMessage("/pinfo <playername>");
            return true;
        }
         $name = strtolower($args[0]);
         $player = Server::getInstance()->getPlayerExact($name);
            if($player instanceof Player){
        /* $ip = $player->getNetworkSession()->getIp();
		$name = $player->getName();
		$ex = $player->getNetworkSession()->getPlayerInfo()->getExtraData();
	    $data = json_decode(file_get_contents(Main::getInstance()->getDataFolder()."data.json"), true);
		$alt = $this->getAlt($data, $name, $ip, $ex);
        $a = $this->ALTTOSTRING($alt, $name);*/
         $sender->sendMessage("§e» §a{$player->getName()} §bInformation:\n" . "§d» §cKills: §f" . DatabaseControler::$kill[$player->getName()] . "\n" . "§d» §cDeaths: §f" . DatabaseControler::$death[$player->getName()] . "\n" . "§d» §cKDR: §f" . round((DatabaseControler::$kill[$player->getName()] !== 0 ? DatabaseControler::$kill[$player->getName()] : 1) / (DatabaseControler::$death[$player->getName()] !== 0 ? DatabaseControler::$death[$player->getName()] : 1), 2) . "\n" . "§d» §cCoins: §f" . number_format(DatabaseControler::$coins[$player->getName()]) . "\n§d» §cDevice Control: §f" . EventListener::$control[$player->getName()] . "\n§d» §cDevice: §f" . EventListener::$device[$player->getName()]);
                } else {
                $player = Server::getInstance()->getOfflinePlayer($name);
					if($player == null) {
						$sender->sendMessage(TextFormat::RED . "Player not found");
					} /*else {
						$name = $player->getName();
						$data = json_decode(file_get_contents(Main::getInstance()->getDataFolder()."data.json"), true);
						$alt = $this->getAltByName($data, $name);
						$sender->sendMessage(TextFormat::GREEN . "§7[§l§cSE§fGA§r§7] §aShowing players who joined that has same data as §b" . $name . "...");
						$a = $this->ALTTOSTRING($alt, $name);
						$sender->sendMessage($a);
					}*/
            }
        }
    }
    
    
    public function ALTTOSTRING(array $alt, string $pname):string{
		$a = ">";
		$ac = count($alt);
		$i = 0;
		foreach($alt as $name => $data){
			$a .= "[§3NAME: ".(($name == $pname) ? "§a" : "§c").$name." : ";
			$a .= "§6TYPE: ";
			$dc = count($data);
			$e = 1;
			foreach($data as $d){
				$d = (($d == "PLAYER") ? "§a" : "§c").$d;
				$a .= $d;
				if($e < $dc){
					$a .= "§r, ";
				}
				$e++;
			}
			if($i < $ac){
				$a .= "§r]";
			}
			$i++;
		}
		return $a;
	}

	public function getAltByName(array $data, string $name) :array{
		$alt = [];
		$alt[$name][] = "PLAYER";
        
        if(isset($data["PFUI"][$name])){
            $ipss = $data["PFUI"][$name];//hashs

            foreach($ipss as $ipp){
                $ips = $data["IP"][$ipp];
                foreach($ips as $name0){
                    if($name0 !== $name){
                        $alt[$name][] = "Ip1";
                    }
                }
            }

            if(isset($data["PFUI0"][$name])){
                $dids1 = $data["PFUI0"][$name];//dids
                foreach($dids1 as $D){
                    $dids0 = $data["DID"][$D];
                    foreach($dids0 as $name0){
                        if($name0 !== $name){
                            $alt[$name][] = "DeviceId1";
                        }
                    }
                }	
            }    
        }
		return $alt;
	}

	public function getAlt(array $data, string $name, string $ip, array $ExtraData) :array{
		$alt = [];
		$alt[$name][] = "PLAYER";

		$dids0 = $data["DID"][$ExtraData["DeviceId"]];//names
		$ips = $data["IP"][hash("fnv164", $ip)];//names
		$ipss = $data["PFUI"][$name];//hashs
		$dids1 = $data["PFUI0"][$name];//dids

		foreach($dids0 as $name0){
			if($name0 !== $name){
				$alt[$name][] = "DeviceId0";
			}
		}

		foreach($ips as $name0){
			if($name0 !== $name){
				$alt[$name][] = "Ip0";
			}
		}

		foreach($ipss as $ipp){
			$ips = $data["IP"][$ipp];
			foreach($ips as $name0){
				if($name0 !== $name){
					$alt[$name][] = "Ip1";
				}
			}
		}

		foreach($dids1 as $D){
			$dids0 = $data["DID"][$D];
			foreach($dids0 as $name0){
				if($name0 !== $name){
					$alt[$name][] = "DeviceId1";
				}
			}
		}
		return $alt;
	}
}