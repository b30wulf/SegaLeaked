<?php

namespace Crasher508\AdvancedReport\provider;

use Crasher508\AdvancedReport\Main;
use Crasher508\AdvancedReport\Report;
use Exception;
use mysqli;
use RuntimeException;

class MySQLProvider extends DataProvider
{

	private mysqli $db;
	private string $tableName;

	/**
	 * SQLiteDataProvider constructor.
	 *
	 * @throws Exception
	 */
	public function __construct() {
		$mysqlData = Main::getInstance()->getConfig()->get("mysql", []);
		$port = (int) $mysqlData["port"] ?? 3306;
		$table = $mysqlData["table"] ?? "advancedreport";
		$this->tableName = $table;
		if (!is_numeric($port)) {
			$port = 3306;
		}
		$this->db = new mysqli(($mysqlData["host"] ?? "127.0.0.1"), ($mysqlData["username"] ?? "root"), ($mysqlData["password"] ?? ""), ($mysqlData["database"] ?? ""), $port);
		if ($this->db->connect_error)
			throw new RuntimeException('Failed to connect to the MySQL database: ' . $this->db->connect_error);
		$this->db->query("CREATE TABLE IF NOT EXISTS " . $table . " (`reportID` INT NOT NULL AUTO_INCREMENT, `reason` TEXT NOT NULL, `reporter` VARCHAR(16) NOT NULL, `player` VARCHAR(16) NOT NULL, `info` TEXT NOT NULL, `date` TEXT, PRIMARY KEY (`reportID`));");
		Main::getInstance()->getLogger()->debug("MySQL data provider registered");
	}

	/**
	 * @param Report $report
	 *
	 * @return bool
	 */
	public function addReport(Report $report) : bool {
		if (!$this->reconnect())
			return false;

		$stmt = $this->db->prepare("INSERT INTO `" . $this->tableName . "` (reason, reporter, player, info, date) VALUES (?, ?, ?, ?, ?);");
		$stmt->bind_param("sssss", $report->reason, $report->reporter, $report->player, $report->info, $report->date);
		$result = $stmt->execute();
		if($result === false)
			return false;
		return true;
	}

	/**
	 * @param Report $report
	 *
	 * @return bool
	 */
	public function removeReport(Report $report) : bool {
		if (!$this->reconnect())
			return false;
		$stmt = $this->db->prepare("DELETE FROM " . $this->tableName . " WHERE reporter = ? AND player = ?;");
		$stmt->bind_param("ss", $report->reporter, $report->player);
		$result = $stmt->execute();
		if($result === false)
			return false;
		return true;
	}

	/**
	 * @param string $reporter
	 * @param string $player
	 *
	 * @return Report|null
	 */
	public function getReport(string $reporter, string $player) : ?Report {
		if (!$this->reconnect())
			return null;
		$stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . " WHERE reporter = ? AND player = ?;");
		$stmt->bind_param('ss', $reporter, $player);
		$results = $stmt->execute();
		if ($results === false)
			return null;
		$results = $stmt->get_result();
		if ($result = $results->fetch_array(MYSQLI_ASSOC)) {
			return new Report((string) $result["reason"], (string) $result["reporter"], (string) $result["player"], (string) $result["info"], (string) $result["date"]);
		}
		return null;
	}

	/**
	 *
	 * @return array
	 */
	public function getAllReports() : array {
		$reports = [];
		if (!$this->reconnect())
			return $reports;

		$stmt = $this->db->prepare("SELECT * FROM " . $this->tableName . ";");
		$results = $stmt->execute();
		if ($results === false)
			return $reports;
		$results = $stmt->get_result();
		while ($result = $results->fetch_array(MYSQLI_ASSOC)) {
			$reports[] = new Report((string) $result["reason"], (string) $result["reporter"], (string) $result["player"], (string) $result["info"], (string) $result["date"]);
		}
		return $reports;
	}

	private function reconnect() : bool {
		if(!$this->db->ping()) {
			$this->close();
			$mysqlData = Main::getInstance()->getConfig()->get("mysql", []);
			$port = (int) $mysqlData["port"] ?? 3306;
			if (!is_numeric($port)) {
				$port = 3306;
			}
			$this->db->connect(($mysqlData["host"] ?? "127.0.0.1"), ($mysqlData["username"] ?? "root"), ($mysqlData["password"] ?? ""), ($mysqlData["database"] ?? ""), $port);
			if(!$this->db->ping()) {
				Main::getInstance()->getLogger()->error("§cCan't reconnect!");
				if ($this->db->connect_error) {
					Main::getInstance()->getLogger()->error('Failed to connect to the MySQL database: ' . $this->db->connect_error);
					return false;
				}
			}
		}
		return true;
	}

	public function close() : void {
		if ($this->db->close())
			Main::getInstance()->getLogger()->debug("MySQL database closed!");
	}

	public function getName() : string {
		return "MySQL";
	}
}