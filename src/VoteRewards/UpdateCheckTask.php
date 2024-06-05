<?php

namespace VoteRewards;

use pocketmine\scheduler\Task;
use pocketmine\utils\Internet;

class UpdateCheckTask extends Task {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick): void {
        $url = "https://poggit.pmmp.io/releases.json?name=VoteRewards";
        $result = Internet::getURL($url);
        $data = json_decode($result, true);
        if (isset($data[0])) {
            $this->plugin->setLatestVersion($data[0]["version"]);
            if (version_compare($this->plugin->getCurrentVersion(), $this->plugin->getLatestVersion(), '<')) {
                $this->plugin->getLogger()->info("A new version of VoteRewards is available: " . $this->plugin->getLatestVersion() . ". Download it from https://poggit.pmmp.io/p/VoteRewards");
            }
        }
    }
}
