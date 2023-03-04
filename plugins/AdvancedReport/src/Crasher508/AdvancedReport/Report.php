<?php
declare(strict_types=1);
namespace Crasher508\AdvancedReport;

class Report
{

	public string $reason = "", $reporter = "", $player = "", $info = "", $date = "";

	/**
	 * Report constructor.
	 *
	 * @param string $reason
	 * @param string $reporter
	 * @param string $player
	 * @param string $info
	 * @param string $date
	 */
	public function __construct(string $reason, string $reporter, string $player, string $info, string $date) {
		$this->reason = $reason;
		$this->reporter = $reporter;
		$this->player = $player;
		$this->info = $info;
		$this->date = $date;
	}

	/**
	 * @return string
	 */
	public function __toString() : string {
		return $this->reason . " - " . $this->player;
	}
}
