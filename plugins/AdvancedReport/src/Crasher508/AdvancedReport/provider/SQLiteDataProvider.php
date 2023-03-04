<?php
declare(strict_types=1);
namespace Crasher508\AdvancedReport\provider;

use Crasher508\AdvancedReport\Main;
use Crasher508\AdvancedReport\Report;
use SQLite3;
use SQLite3Stmt;

class SQLiteDataProvider extends DataProvider
{
	private SQLite3 $db;
	private SQLite3Stmt $sqlAddReport, $sqlRemoveReport, $sqlGetReport, $sqlGetAllReports;

	/**
	 * SQLiteDataProvider constructor.
	 */
	public function __construct() {
		$this->db = new SQLite3(Main::getInstance()->getDataFolder() . "reports.db");
		$this->db->exec("CREATE TABLE IF NOT EXISTS reports
			(reason TEXT, reporter TEXT, player TEXT, info TEXT, date TEXT);");
		$this->sqlAddReport = $this->db->prepare("INSERT OR REPLACE INTO reports (reason, reporter, player, info, date) VALUES (:reason, :reporter, :player, :info, :date);");
		$this->sqlRemoveReport = $this->db->prepare("DELETE FROM reports WHERE reporter = :reporter AND player = :player;");
		$this->sqlGetReport = $this->db->prepare("SELECT reason, reporter, player, info, date FROM reports WHERE reporter = :reporter AND player = :player;");
		$this->sqlGetAllReports = $this->db->prepare("SELECT reason, reporter, player, info, date FROM reports;");
		Main::getInstance()->getLogger()->debug("SQLite data provider registered");
	}

	/**
	 * @param Report $report
	 *
	 * @return bool
	 */
	public function addReport(Report $report) : bool{
		$stmt = $this->sqlAddReport;
		$stmt->bindValue(":reason", $report->reason, SQLITE3_TEXT);
		$stmt->bindValue(":reporter", $report->reporter, SQLITE3_TEXT);
		$stmt->bindValue(":player", $report->player, SQLITE3_TEXT);
		$stmt->bindValue(":info", $report->info, SQLITE3_TEXT);
		$stmt->bindValue(":date", $report->date, SQLITE3_TEXT);
		$stmt->reset();
		$result = $stmt->execute();
		if($result === false) {
			return false;
		}
		return true;
	}

	/**
	 * @param Report $report
	 *
	 * @return bool
	 */
	public function removeReport(Report $report) : bool{
		$stmt = $this->sqlRemoveReport;
		$stmt->bindValue(":reporter", $report->reporter, SQLITE3_TEXT);
		$stmt->bindValue(":player", $report->player, SQLITE3_TEXT);
		$stmt->reset();
		$result = $stmt->execute();
		if($result === false) {
			return false;
		}
		return true;
	}

	/**
	 * @param string $reporter
	 * @param string $player
	 *
	 * @return Report|null
	 */
	public function getReport(string $reporter, string $player) : ?Report {
		$this->sqlGetReport->bindValue(":reporter", $reporter, SQLITE3_TEXT);
		$this->sqlGetReport->bindValue(":player", $player, SQLITE3_TEXT);
		$this->sqlGetReport->reset();
		$result = $this->sqlGetReport->execute();
		if($val = $result->fetchArray(SQLITE3_ASSOC)) {
			return new Report((string) $val["reason"], (string) $val["reporter"], (string) $val["player"], (string) $val["info"], (string) $val["date"]);
		}else{
			return null;
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getAllReports() : array{
		$stmt = $this->sqlGetAllReports;
		$reports = [];
		$stmt->reset();
		$result = $stmt->execute();
		while($val = $result->fetchArray(SQLITE3_ASSOC)) {
			$reports[] = new Report((string) $val["reason"], (string) $val["reporter"], (string) $val["player"], (string) $val["info"], (string) $val["date"]);
		}
		return $reports;
	}

	public function close() : void {
		$this->db->close();
		Main::getInstance()->getLogger()->debug("SQLite database closed!");
	}

	public function getName() : string {
		return "SQLite3";
	}
}