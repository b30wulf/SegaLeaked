<?php

namespace Rushil13579\EasyModeration\Commands;

use DateTime;
use InvalidArgumentException;
use pocketmine\command\{Command, CommandSender};
use pocketmine\plugin\Plugin;
use Rushil13579\EasyModeration\Main;
use Rushil13579\EasyModeration\utils\Expiry;
use pocketmine\Server;

class Tempban extends Command {

    /** @var Main */
    private $main;

    public function __construct(Main $main) {
        $this->main = $main;

        parent::__construct('tempban', 'Temporarily prevent a player from accessing this server', '/tempban <player> <time> [reason...]');
        $this->setPermission('easymoderation.tempban');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$this->testPermission($sender)) {
            $sender->sendMessage(Main::PREFIX . ' §cYou do not have permission to use this command');
            return false;
        }

        if(count($args) < 2) {
            $sender->sendMessage(Main::PREFIX . ' §cUsage: /tempban <player> <time> [reason...]');
            return false;
        }

        $player = $this->main->getServer()->getPlayerExact($args[0]);

        if(count($args) > 2) {
            $reason = implode(' ', array_slice($args, 2));
        } else {
            $reason = 'Banned by administrator';
        }

        $banList = $sender->getServer()->getNameBans();
        $sendername = $sender->getName();

        if($player != null) {
            $playername = $player->getName();
        } else {
            $playername = $args[0];
        }

        if($banList->isBanned($playername)) {
            $sender->sendMessage(Main::PREFIX . ' §cThis player is already banned');
            return false;
        }

        try {
            $expiry = new Expiry($args[1]);
            $expiryToString = Expiry::expirationTimerToString($expiry->getDate(), new DateTime());

            $banList->addBan($playername, $reason, $expiry->getDate(), $sendername);

            if($player != null) {
                $msg = "§l§cNetwork Tempban\n§r§cBanned by: §f{$sendername}\n§cReason: §f{$reason}\n§cBan time: §f{$expiryToString}\n§aAppeal at §f: https://dsc.gg/seganetwork"; //$sendername, $expiryToString
                $player->kick($msg, false);
            }

            $msg = "§f===================\n§l§c     Network TempBan\n\n§r§cPlayer: §f{$playername}\n§cReason: §f{$reason}\n§cBan time: §f{$expiryToString}\n§cBanned By: §f{$sendername}\n§f===================";
            Server::getInstance()->broadcastMessage($msg);

            if($this->main->cfg->get('tempban-discord-post') == 'enabled') {
                $webhook = $this->main->cfg->get('tempban-webhook');

                $msg = "__**NEW TEMP BAN**__\nPlayer Banned: $playername\nBan Time: $expiryToString\nBanned By: $sendername\nReason: $reason";
                $this->main->postToDiscord($webhook, $msg);
            }
        } catch(InvalidArgumentException $msg) {
            $sender->sendMessage($msg->getMessage());
        }
        return true;
    }

    public function getPlugin(): Plugin {
        return $this->main;
    }
}