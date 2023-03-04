<?php

namespace Crasher508\AdvancedReport\forms;

use Crasher508\AdvancedReport\Main;
use Crasher508\AdvancedReport\Report;
use DateTime;
use DateTimeZone;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;

class SimpleReportForm extends CustomForm {

    public function __construct(array $players) {
        parent::__construct(
			"§l§aAdvancedReport",
			[
				new Dropdown(
					"player", Main::getInstance()->translateString("addreport.formchoose"), $players
				),
				new Dropdown(
					"reason", Main::getInstance()->translateString("addreport.formreason"), Main::getInstance()->getConfig()->get("Reasons", ["Spam"])
				),
				new Input(
					"problem", Main::getInstance()->translateString("addreport.forminfo"),
				)
			],
			function (Player $player, CustomFormResponse $response) use ($players) : void {
				$reportet = $players[$response->getInt("player")];
				$reason = Main::getInstance()->getConfig()->get("Reasons", ["Spam"])[$response->getInt("reason")];
				$info = $response->getString("problem");
				if ($info !== "") {
					if (Main::getInstance()->getProvider()->getReport($player->getName(), $reportet) === null) {
						$time = new DateTime("now", new DateTimeZone("Europe/Berlin"));
						$time = $time->format("d.m.Y H:i");
						$report = new Report($reason, $player->getName(), $reportet, $info, $time);
						Main::getInstance()->getProvider()->addReport($report);
						$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("addreport.success", [$reportet]));
						if (Main::getInstance()->getConfig()->get("SendToDiscord")) {
							$msg = Main::getInstance()->translateString("addreport.discord", [$player->getName(), $reportet, $reason, $info]);
							Main::getInstance()->sendMessage($msg);
						}
					} else {
						$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("addreport.alwaysreport"));
					}
				} else {
					$player->sendMessage(Main::getInstance()->prefix . Main::getInstance()->translateString("addreport.emptyinput"));
				}
			}
		);
    }
}