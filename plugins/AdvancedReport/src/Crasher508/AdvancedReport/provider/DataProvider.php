<?php
declare(strict_types=1);
namespace Crasher508\AdvancedReport\provider;

use Crasher508\AdvancedReport\Report;
use Crasher508\AdvancedReport\Main;

abstract class DataProvider
{

	/**
	 * @param Report $report
	 *
	 * @return bool
	 */
	public abstract function addReport(Report $report) : bool;

	/**
	 * @param Report $report
	 *
	 * @return bool
	 */
	public abstract function removeReport(Report $report) : bool;

	/**
	 * @param string $reporter
	 * @param string $player
	 *
	 * @return Report|null
	 */
	public abstract function getReport(string $reporter, string $player) : ?Report;

	/**
	 * @return Report[]
	 */
	public abstract function getAllReports() : array;

	/**
	 * @return void
	 */
	public abstract function close() : void;

	/**
	 * @return string
	 */
	public abstract function getName() : string;
}