<?php

namespace VoteRewards;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class VoteCommand extends Command {

    private $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("vote", "Displays the voting link", null, ["voting"]);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if ($sender instanceof Player) {
            $sender->sendMessage("Vote for our server at: https://minecraftpocket-servers.com/");
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }
}
