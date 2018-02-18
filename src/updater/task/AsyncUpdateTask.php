<?php
namespace updater\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Utils;
use const pocketmine\PLUGIN_PATH;

class AsyncUpdateTask extends AsyncTask {
	/** @var string $plugin */
	private $plugin;
	/** @var string $localFile */
	private $localFile;

	/**
	 * AsyncUpdateTask constructor.
	 *
	 * @param string $plugin
	 * @param string $localFile
	 */
	public function __construct(string $plugin, string $localFile) {
		$this->plugin = $plugin;
		$this->localFile = $localFile;
	}
	public function onRun() {
		$result = Utils::getURL("https://poggit.pmmp.io/releases.min.json?name=$this->plugin"."&latest-only", 30, [], $error);
		$plugins = json_decode($result, true);
		$data = $plugins[0];
		$this->setResult(
			version_compare($data["version"], Server::getInstance()->getPluginManager()->getPlugin($this->plugin)->getDescription()->getVersion()) === 1,
			false
		);
		touch(PLUGIN_PATH.$this->plugin."Temp.phar");
		file_put_contents(PLUGIN_PATH.$this->plugin."Temp.phar", Utils::getURL($data["artifact_url"]));
	}

	/**
	 * @param Server $server
	 *
	 * @throws \Exception
	 */
	public function onCompletion(Server $server) {
		if($this->getResult()) {
			$newFilename = PLUGIN_PATH.$this->plugin."Temp.phar";
			$localFilename = $this->localFile;
			$backupTarget = null;
			try {
				@chmod($newFilename, fileperms($localFilename));
				if (!ini_get('phar.readonly')) {

					$phar = new \Phar($newFilename);
					unset($phar);
				}


				if ($backupTarget && file_exists($localFilename)) {
					@copy($localFilename, $backupTarget);
				}

				rename($newFilename, $localFilename);

				return;
			} catch (\Exception $e) {
				if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
					throw $e;
				}

				return;
			}
		}
		return;
	}
}