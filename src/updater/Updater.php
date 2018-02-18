<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 2/16/2018
 * Time: 9:11 AM
 */

namespace updater;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use updater\task\AsyncUpdateTask;

class Updater {
	public static function update(PluginBase $plugin, string $path) {
		Server::getInstance()->getScheduler()->scheduleAsyncTask(new AsyncUpdateTask($plugin->getName(), $path));
	}
}