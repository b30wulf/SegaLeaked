<?php

namespace SegaCore\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\lang\Translatable;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use SegaCore\Core\PlayerManager;

class PingCommand extends Command{

	public function __construct()
	{
		parent::__construct('ping', TextFormat::RESET . 'View your latency');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args)
	{
		if (!isset($args[0])) {
			if ($sender instanceof Player) {
				$sender->sendMessage(TextFormat::RED . 'Your Ping: ' . TextFormat::WHITE . $sender->getNetworkSession()->getPing());
			}
		} else {
			$target = Server::getInstance()->getPlayerByPrefix($args[0]);
			if (is_null($target)) {
				$sender->sendMessage(TextFormat::WHITE . 'The Player ' . $target . ' wasn\'t found.');
				return;
			} elseif ($target instanceof Player) {
				$sender->sendMessage(TextFormat::RED . $target->getDisplayName() . '\'s Ping: ' . TextFormat::WHITE . $target->getNetworkSession()->getPing());
			}
		}
	}
}