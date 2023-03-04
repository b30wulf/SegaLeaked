<?php

namespace Alt;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class MainClass extends PluginBase implements Listener {
	public function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(TextFormat::YELLOW . "You can only alias a player when they are online!!");
	}

	public function onJoin(PlayerJoinEvent $event) {
		if(!is_dir($this->getDataFolder() . "players/")) {
			@mkdir($this->getDataFolder() . "players/", 0777, true);
		}
		$name = $event->getPlayer()->getDisplayName();
		$player = $event->getPlayer();
        $ip = $player->getNetworkSession()->getIp();
		$file = new Config($this->getDataFolder() . "players/" . $ip . ".txt", CONFIG::ENUM);
		$file->set($name);
		$file->save();
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		switch($command->getName()) {
			case "alias":
				if(!isset($args[0])) {
					$sender->sendmessage(TextFormat::RED . "Usage: " . $command->getUsage() . "");
					return true;
				}
				$name = strtolower($args[0]);
				$player = $this->getServer()->getPlayerExact($name);
				if($player instanceOf Player) {
					$ip = $player->getNetworkSession()->getIp();
					$file = new Config($this->getDataFolder() . "players/" . $ip . ".txt");
					$names = $file->getAll(true);
					$names = implode(', ', $names);
					$sender->sendMessage(TextFormat::GREEN . "§7[§l§cSE§fGA§r§7] §aShowing players who joined from the same IP as §b" . $name . "...");
					$sender->sendMessage(TextFormat::AQUA . $names);
					return true;
					break;
				} else {
					if($player == null) {
						$sender->sendMessage(TextFormat::RED . "Player not found");
					} else {
                    if(!$player->isOnline());
						$sender->sendMessage(TextFormat::RED . "the player must be online");
					}
                }
				}
				return true;
		}
}