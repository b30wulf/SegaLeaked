<?php
declare(strict_types=1);
namespace Crasher508\AdvancedReport;

use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use Crasher508\AdvancedReport\provider\DataProvider;
use Crasher508\AdvancedReport\provider\MySQLProvider;
use Crasher508\AdvancedReport\provider\SQLiteDataProvider;
use Crasher508\AdvancedReport\commands\ReportCommand;
use Exception;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase
{
	private static Main $instance;
	private ?DataProvider $dataProvider = null;
	private ?Language $baseLang = null;

	public string $prefix = "§l§cGuardian §d> §r";

	public static function getInstance() : Main {
		return self::$instance;
	}

	public function getLanguage() : Language {
		return $this->baseLang;
	}

	public function getProvider() : DataProvider {
		return $this->dataProvider;
	}

	public function onLoad() : void {
		self::$instance = $this;
		$this->reloadConfig();
		$this->baseLang = new Language(($this->getConfig()->get("Language", Language::FALLBACK_LANGUAGE)), $this->getFile() . "resources/");
		try {
			$this->dataProvider = match ($this->getConfig()->get("provider", "sqlite")) {
				"mysql" => new MySQLProvider(),
				default => new SQLiteDataProvider()
			};
		} catch(Exception $exception) {
			$this->getLogger()->error($exception->getMessage() . ", using SQLite3 provider");
			$this->dataProvider = new SQLiteDataProvider();
		}
		$this->getLogger()->info($this->dataProvider->getName() . " provider activated!");
	}

	public function onEnable() : void {
		$this->getServer()->getCommandMap()->register("report", new ReportCommand());
	}

	/**
	 * @param string      $str
	 * @param array       $params
	 * @param string|null $onlyPrefix
	 *
	 * @return string
	 */
	public function translateString(string $str, array $params = [], string $onlyPrefix = null) : string {
        return $this->getLanguage()->translateString($str, $params, $onlyPrefix);
    }

	/**
	 * @param string $msg
	 *
	 * @return void
	 */
	public function sendMessage(string $msg) : void {
		if (!($url = $this->getConfig()->get("Webhook")))
			return;

        $webhook = new Webhook($url);
        $message = new Message();
        $message->setUsername($this->getConfig()->get("WebhookName", "AdvancedReport"));
        $message->setContent($msg);
        $webhook->send($message);
    }

	public function onDisable() : void {
		$this->dataProvider?->close();
	}
}
