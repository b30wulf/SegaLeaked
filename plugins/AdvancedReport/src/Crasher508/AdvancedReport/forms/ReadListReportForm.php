<?php

namespace Crasher508\AdvancedReport\forms;

use Crasher508\AdvancedReport\Main;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class ReadListReportForm extends MenuForm {

    public function __construct() {
        $reports = Main::getInstance()->getProvider()->getAllReports();
		$buttons = [];
		foreach ($reports as $report) {
			$buttons[] = new MenuOption("§c" . $report->player . " - " . $report->reason);
		}
        parent::__construct(
			"§l§aAdvancedReport Dashboard",
			"",
			$buttons,
			function (Player $player, int $selected) use ($reports) : void {
				$report = $reports[$selected];
				$form = new ReadReportForm($report);
				$player->sendForm($form);
			}
		);
    }
}