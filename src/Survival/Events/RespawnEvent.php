<?php

namespace Survival\Events;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\utils\Config;

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

        if(!is_null($level)) {
            if($this->plugin->messages["respawn"]) {
                $spawnPosition = $level->getSafeSpawn();
                
                $event->setRespawnPosition($spawnPosition);
                
                $player->setGamemode(0);
            }
                
                            
            $name = strtolower($player->getName());
            $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerData.yml", Config::YAML);

            $playerData = $data->get($name, []);

            $playerData["pos"] = "death";
            $data->set($name, $playerData);
            $data->save();
        }
    }
}