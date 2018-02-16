<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 2/16/2018
 * Time: 9:15 AM
 */

namespace jasonwynn10;

use Humbug\SelfUpdate\Updater;
use pocketmine\plugin\Plugin;

class AutoUpdateTester {
    /** @var Updater $updater */
    private $updater;

    public function __construct(Plugin $plugin, string $file, ?string $name = null, $f) {
        $this->updater = new Updater($file, false, Updater::STRATEGY_GITHUB);
        $this->updater->getStrategy()->setCurrentLocalVersion($plugin->getDescription()->getVersion());
        $this->updater->getStrategy()->download($this->updater);
    }
}