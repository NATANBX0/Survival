<?php

namespace Survival\Events;

use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\Config;
use Survival\Tasks\RestorePositionTask;

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
            $this->plugin->playerManager->savePlayerPosition($player, $levelOrigin);
            $this->plugin->playerManager->savePlayerInventory($player, $levelOrigin, true);
        }

        $filePathTarget = $this->plugin->getDataFolder() . $levelTarget->getFolderName();

        if(file_exists($filePathTarget)) {
            $data = new Config($this->plugin->getDataFolder() . $levelTarget->getFolderName() . "/playerData.yml", Config::YAML);

            $playerData = $data->get(strtolower($player->getName()), []);

            if(!isset($playerData) || $playerData["pos"] === "death") {
                return;
            }

            $this->plugin->getServer()->getScheduler()->scheduleDelayedTask(
                new RestorePositionTask($this->plugin, $player, $levelTarget),
                0
            );
            $this->plugin->playerManager->restorePlayerInventory($player, $levelTarget, true);
        }
    }
}