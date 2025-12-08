<?php

namespace Survival;

use pocketmine\plugin\PluginBase;
use Survival\Commands\ListSurvivalCommand;
use Survival\Commands\RemoveSurvivalCommand;
use Survival\Commands\SetSurvivalCommand;
use Survival\Events\DisablePluginEvent;
use Survival\Events\PlayerChangeWorldEvent;
use Survival\Events\PlayerRespawnEvents;
use Survival\Events\PlayerQuitEvents;
use Survival\Utils\InventoryManager;

class Main extends PluginBase {

    public $inventoryManager;

    public $prefix;

    public $messages;

    public function onEnable()
    {
        @mkdir($this->getDataFolder());

        $this->inventoryManager = new InventoryManager($this);

        /** @disregard */
        $this->getCommand("setsurvival")->setExecutor(new SetSurvivalCommand($this));
        /** @disregard */
        $this->getCommand("rmsurvival")->setExecutor(new RemoveSurvivalCommand($this), $this);
        /** @disregard */
        $this->getCommand("listsurvival")->setExecutor(new ListSurvivalCommand($this), $this);

        $this->getServer()->getPluginManager()->registerEvents(new PlayerQuitEvents($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new PlayerChangeWorldEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new DisablePluginEvent($this), $this);
        //$this->getServer()->getPluginManager()->registerEvents(new PlayerRespawnEvents($this), $this);

        $this->getLogger()->info("Plugin habilitado com sucesso!");

        $this->saveDefaultConfig();
        $this->reloadConfig();

        $this->prefix = $this->getConfig()->get("prefix");

        $this->messages = $this->getConfig()->getAll();
    }


    public function deleteFolder($path) {
        if(!is_dir($path)) return false;

        $files = array_diff(scandir($path), ['.', '..']);

        foreach($files as $file) {
            $filePath = $path . DIRECTORY_SEPARATOR . $file;

            if(is_dir($filePath)) {
                $this->deleteFolder($filePath);
            } else {
                unlink($filePath);
            }
        }

        return rmdir($path);
    }
}