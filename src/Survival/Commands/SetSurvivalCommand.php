<?php

namespace Survival\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class SetSurvivalCommand implements CommandExecutor {

    private $plugin;

    private $data;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        if(count($args) < 1) {
            if($sender instanceof Player) {
                $level = $sender->getLevel()->getFolderName();
            }
        } else {
            $level = $args[0];
        }

        $path = $this->plugin->getDataFolder() . $level . "/";

        if(!is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        $message = str_replace("{level}", $level, $this->plugin->messages["set-survival"]);

        $sender->sendMessage($this->plugin->prefix . $message);

        return true;
    }
}