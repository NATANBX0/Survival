<?php

namespace Survival\Tasks;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class RestorePositionTask extends PluginTask {
    
    private $player;
    private $level;

    public function __construct($plugin, $player, $level)
    {
        parent::__construct($plugin);
        $this->player = $player;
        $this->level = $level;
    }

    public function onRun($currentTick){
        
        if(!$this->player->isOnline())
            return;

        $this->getOwner()->playerManager->restorePlayerPosition($this->player, $this->level);
    }
}