<?php

namespace Survival\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

class ListSurvivalCommand implements CommandExecutor {

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args)
    {
        $path = $this->plugin->getDataFolder();
        $worldFolders = array_filter(glob($path . "*"), 'is_dir');
        $worlds = [];

        foreach($worldFolders as $world)
            $worlds[] = basename($world);

        if(empty($worlds)) {
            $noSurvivalWorldsMessage = $this->plugin->messages["no-survival-worlds"];
            $sender->sendMessage($this->plugin->prefix . $noSurvivalWorldsMessage);
            return true;
        }

        $survivalWorlds = implode(", ", $worlds);

        $survivalList = str_replace("{list}", $survivalWorlds, $this->plugin->messages["survival-list"]);

        $sender->sendMessage($this->plugin->prefix . $survivalList);
        
        return true;
    }
}