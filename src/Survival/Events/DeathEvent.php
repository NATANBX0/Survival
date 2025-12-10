<?php

namespace Survival\Events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class DeathEvent implements Listener {
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
    public function onPlayerRespawn(PlayerDeathEvent $event) {

        $player = $event->getPlayer();

        $level = $player->getLevel();

        if(file_exists($this->plugin->getDataFolder() . $level->getName())) {
            $this->plugin->players[$player->getName()] = $level->getName();
            return;
        }

        unset($this->plugin->players[$player->getName()]);
    }
}