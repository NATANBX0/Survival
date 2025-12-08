<?php

namespace Survival\Events;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;

class PlayerChangeWorldEvent implements Listener {

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @priority LOWEST
     */
    public function onEntityLevelChange(EntityLevelChangeEvent $event) {
        $player = $event->getEntity();

        $levelOrigin = $event->getOrigin();

        $filePathOrigin = $this->plugin->getDataFolder() . $levelOrigin->getFolderName();

        if(file_exists($filePathOrigin)) {
            $this->plugin->inventoryManager->savePlayerInventory($player, $levelOrigin);
        }

        $levelTarget = $event->getTarget();

        $filePathTarget = $this->plugin->getDataFolder() . $levelTarget->getFolderName();

        if($levelOrigin === $levelTarget)
            return;

        if(file_exists($filePathTarget)) {
            $this->plugin->inventoryManager->restorePlayerInventory($player, $levelTarget);
        }
    }
}