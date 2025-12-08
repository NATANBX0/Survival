<?php

namespace Survival\Events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\EventPriority;

class PlayerQuitEvents implements Listener {

    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerQuitEvent $event
     * @priority LOW
     */

    public function onQuit(PlayerQuitEvent $event) {

        $player = $event->getPlayer();

        $level = $player->getLevel();

        $filePath = $this->plugin->getDataFolder() . $level->getFolderName();

        if(file_exists($filePath)) {
            $this->plugin->inventoryManager->restorePlayerInventory($player, $level);
        }
    }
}