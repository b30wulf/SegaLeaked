<?php

namespace SegaCore\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\lang\KnownTranslationFactory;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
class FakeMSG extends Command{

    public function __construct(string $name, Translatable|string $description = "")
    {
        parent::__construct($name, $description);
        parent::setAliases(["wisper", "tell", "c", "w"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
		if(count($args) < 2){
			throw new InvalidCommandSyntaxException();
		}

		$player = $sender->getServer()->getPlayerByPrefix(array_shift($args));

		if($player === $sender){
			$sender->sendMessage(KnownTranslationFactory::commands_message_sameTarget()->prefix(TextFormat::RED));
			return true;
		}

		if($player instanceof Player){
			$message = implode(" ", $args);
			$sender->sendMessage(KnownTranslationFactory::commands_message_display_outgoing($player->getDisplayName(), $message)->prefix(TextFormat::GRAY . TextFormat::ITALIC));
			$name = $sender instanceof Player ? $sender->getDisplayName() : $sender->getName();
			$player->sendMessage(KnownTranslationFactory::commands_message_display_incoming($name, $message)->prefix(TextFormat::GRAY . TextFormat::ITALIC));
			Command::broadcastCommandMessage($sender, KnownTranslationFactory::commands_message_display_outgoing($player->getDisplayName(), $message), false);
            $p = $sender->getServer()->getOnlinePlayers();
            foreach($p as $pl) {
                if($pl->hasPermission("sega.staff")){
                    $pl->sendMessage("§l§6StaffChat §e> §r§9 MSG §g{$sender->getName()} §6: to §6 {$player->getDisplayName()} §3>>" . $message);
                }
            }
            return true;
		}else{
			$sender->sendMessage(KnownTranslationFactory::commands_generic_player_notFound());
            return true;
        }
    }
}