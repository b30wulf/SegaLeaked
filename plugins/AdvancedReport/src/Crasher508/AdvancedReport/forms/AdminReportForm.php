<?php

namespace Crasher508\AdvancedReport\forms;

use Crasher508\AdvancedReport\Main;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class AdminReportForm extends MenuForm {

    public function __construct() {
        parent::__construct(
			"§l§aAdvancedReport Dashboard",
			Main::getInstance()->translateString("adminreport.content"),
			[
				new MenuOption(Main::getInstance()->translateString("adminreport.close")),
				new MenuOption(Main::getInstance()->translateString("adminreport.create")),
				new MenuOption(Main::getInstance()->translateString("adminreport.manage"))
			],
			function (Player $player, int $selected) : void {
				switch ($selected) {
					case 0:
						break;
					case 1:
						$players = [];
						foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $online) {
							if ((($add = $online->getName()) !== $player->getName()) and !$online->hasPermission("report.bypass")) {
								$players[] = $add;
							}
						}
						if (count($players) < 1) {
							$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("addreport.noplayers"));
							return;
						}
						$form = new SimpleReportForm($players);
						$player->sendForm($form);
						break;
					case 2:
						if ($player->hasPermission("report.command.read")) {
							$reports = Main::getInstance()->getProvider()->getAllReports();
							if (count($reports) > 0) {
								$form = new ReadListReportForm();
								$player->sendForm($form);
							} else {
								$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("noreports"));
							}
						} else {
							$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("noperm"));
						}
						break;
				}
			}
		);
    }
}