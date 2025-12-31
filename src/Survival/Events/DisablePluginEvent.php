<?php

namespace Survival\Events;

use pocketmine\event\Listener;
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\utils\Config;

class DisablePluginEvent implements Listener {

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function onDisablePlugin(PluginDisableEvent $event) {

        foreach($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $level = $player->getLevel();

            $path = $this->plugin->getDataFolder() . $level->getFolderName();
            
            if(file_exists($path)) {
                $this->plugin->playerManager->savePlayerInventory($player, $level, false);
                $this->plugin->playerManager->savePlayerPosition($player, $level);
            }
        }
    }
}