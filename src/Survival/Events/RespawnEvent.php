<?php

namespace Survival\Events;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;

class RespawnEvent implements Listener {
    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Summary of onPlayerRespawn
     * @param PlayerRespawnEvent $event
     * @return void
     * @priority HIGHEST
     */
    public function onPlayerRespawn(PlayerRespawnEvent $event) {

        $player = $event->getPlayer();

        if(!isset($this->plugin->players[$player->getName()]))
            return;

        $playerLevel = $this->plugin->players[$player->getName()];

        $level = $this->plugin->getServer()->getLevelByName($playerLevel);

        if($level !== null) {
            
            if($level !== null) {
                $spawnPosition = $level->getSafeSpawn();

                $event->setRespawnPosition($spawnPosition);

                $player->setGamemode(0);
            }
        }
    }
}