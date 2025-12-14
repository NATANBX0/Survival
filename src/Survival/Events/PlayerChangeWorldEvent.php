<?php

namespace Survival\Events;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

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

        if(!$player instanceof Player)
            return;

        $levelOrigin = $event->getOrigin();
        
        $levelTarget = $event->getTarget();

        if($levelOrigin->getFolderName() === $levelTarget->getFolderName())
            return;

        $filePathOrigin = $this->plugin->getDataFolder() . $levelOrigin->getFolderName();

        if(file_exists($filePathOrigin)) {
            $this->plugin->playerManager->savePlayerInventory($player, $levelOrigin, true);
        }

        $filePathTarget = $this->plugin->getDataFolder() . $levelTarget->getFolderName();

        if(file_exists($filePathTarget)) {
            $this->plugin->playerManager->restorePlayerInventory($player, $levelTarget, true);
        }
    }
}