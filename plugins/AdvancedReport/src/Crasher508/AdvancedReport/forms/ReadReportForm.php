<?php

namespace Crasher508\AdvancedReport\forms;

use Crasher508\AdvancedReport\Main;
use Crasher508\AdvancedReport\Report;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class ReadReportForm extends MenuForm {

    public function __construct(Report $report) {
        parent::__construct(
			"§l§aAdvancedReport Dashboard",
			Main::getInstance()->translateString("readreport.content", [$report->player, $report->reason, $report->reporter, $report->date, $report->info]),
			[new MenuOption(Main::getInstance()->translateString("readreport.delte"))],
			function (Player $player, int $selected) use ($report) : void {
				if ($player->hasPermission("report.command.delete")) {
					Main::getInstance()->getProvider()->removeReport($report);
					$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("readreport.successdelete", [$report->player, $report->reason]));
				} else {
					$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("noperm"));
				}
			}
		);
    }
}