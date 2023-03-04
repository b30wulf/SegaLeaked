<?php

namespace SegaCore\Core;

use pocketmine\utils\TextFormat;

class RankManager {

    public static function getRankFormat(string $rank): string {
        $rank = strtoupper($rank);

        switch($rank){
            case "PLAYER":
                return TextFormat::GRAY;
            break;
            case "VIP":
                return TextFormat::GREEN . "[VIP]";
            break;
            case "VIP+":
                return TextFormat::GREEN . "[VIP" . TextFormat::YELLOW . "+" . TextFormat::GREEN . "]";
            break;
            case "MVP":
                return TextFormat::AQUA . "[MVP]";
            break;
            case "MVP+":
                return TextFormat::AQUA . "[MVP" . TextFormat::YELLOW . "+" . TextFormat::AQUA . "]";
            break;
            case "MVP++":
                return TextFormat::AQUA . "[MVP" . TextFormat::YELLOW . "++" . TextFormat::AQUA . "]";
            break;
            case "OWNER":
                return TextFormat::RED . "[OWNER]";
            break;
            case "ADMIN":
                return TextFormat::RED . "[ADMIN]";
            break;
            default:
                return TextFormat::GRAY;
            break;
        }

    }
}
