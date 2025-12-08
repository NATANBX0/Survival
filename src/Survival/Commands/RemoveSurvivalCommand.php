<?php

namespace Survival\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class RemoveSurvivalCommand implements CommandExecutor {

    private $plugin;

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

        
        if(!file_exists($this->plugin->getDataFolder() . $level)) {
            $messageNotFound = str_replace("{level}", $level, $this->plugin->messages["not-found"]);
            $sender->sendMessage($this->plugin->prefix . $messageNotFound);
            return true;
        }

        $this->plugin->deleteFolder($this->plugin->getDataFolder() . $level);

        $messageRemoved = str_replace("{level}", $level, $this->plugin->messages["removed-survival"]);

        $sender->sendMessage($this->plugin->prefix . $messageRemoved);

        return true;
    }
}