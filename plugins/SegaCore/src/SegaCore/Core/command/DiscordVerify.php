<?php

namespace SegaCore\Core\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use SegaCore\Core\arena\Arena;
use SegaCore\Core\EventListener;
use SegaCore\Core\PlayerManager;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Embed;
class DiscordVerify extends Command{

    public function __construct(string $name, Translatable|string $description = "Verify yourself in our Discord server!!")
    {
        parent::__construct($name, $description);
        parent::setAliases(["discord"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (count($args) < 1) {
            $prefix = "§l§cSE§fGA §e»§r ";
            $sender->sendMessage($prefix . "Usage: /discord Username#0000");
            return true;
        }
            if($sender instanceof Player) {
                $prefix = "§l§cSE§fGA §e»§r ";
                $webHook = new Webhook("https://discord.com/api/webhooks/988083475276005376/x7l2mXpjz6wKJ7rYmN3dDvt4NXX0uGsshQa1n8qYiK2heC68wDN1ux5vxw4ecUKtm9Ie");
                $msg = new Message();
                $msg->setContent($sender->getName() . " | " . $args[0]);
                $webHook->send($msg); 
               $sender->sendMessage($prefix . "§aGreat!, you are now verified!");
            } else {
                 if(!$sender instanceof Player) {
                $prefix = "§l§cSE§fGA §e»§r ";
                $sender->sendMessage($prefix . "ur not a player");
            }
        return true;
           } 
           } 