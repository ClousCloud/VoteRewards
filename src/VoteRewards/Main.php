<?php

namespace VoteRewards;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\Server;
use pocketmine\utils\Internet;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class Main extends PluginBase implements Listener {

    private $apiKey;
    private $config;
    private $currentVersion;
    private $latestVersion;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $this->apiKey = $this->config->get("api-key");
        $this->currentVersion = $this->getDescription()->getVersion();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("vote", new VoteCommand($this));
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new UpdateCheckTask($this), 20 * 60 * 60); // Check for updates every hour
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $this->checkVote($player->getName());
    }

    public function checkVote(string $playerName): void {
        $url = "https://minecraftpocket-servers.com/api-vote/?object=votes&element=claim&key=" . $this->apiKey . "&username=" . $playerName;
        $result = Internet::getURL($url);
        $data = json_decode($result, true);
        if (isset($data["voted"]) && $data["voted"] === true) {
            $this->rewardPlayer($playerName);
        }
    }

    public function rewardPlayer(string $playerName): void {
        $player = $this->getServer()->getPlayerExact($playerName);
        if ($player !== null && $player->isOnline()) {
            $rewards = $this->config->get("rewards", []);
            foreach ($rewards as $reward) {
                $this->getServer()->dispatchCommand($player, str_replace("{player}", $playerName, $reward));
            }
            $player->sendMessage("Thank you for voting! You have received your rewards.");
        }
    }

    public function getCurrentVersion(): string {
        return $this->currentVersion;
    }

    public function getLatestVersion(): string {
        return $this->latestVersion;
    }

    public function setLatestVersion(string $version): void {
        $this->latestVersion = $version;
    }
}
