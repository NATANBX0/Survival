<?php

namespace Survival;

use pocketmine\plugin\PluginBase;
use Survival\Commands\DelHomeCommand;
use Survival\Commands\HomeCommand;
use Survival\Commands\SetHomeCommand;
use Survival\Commands\SurvivalListSubCommand;
use Survival\Commands\SurvivalRemoveSubCommand;
use Survival\Commands\SurvivalCommand;
use Survival\Events\DeathEvent;
use Survival\Events\DisablePluginEvent;
use Survival\Events\PlayerChangeWorldEvent;
use Survival\Events\RespawnEvent;
use Survival\Utils\PlayerManager;

class Main extends PluginBase
{

    public $playerManager;

    public $prefix;

    public $messages;

    public $playerDeath;

    public $players = [];

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        
        $this->getServer()->getCommandMap()->register("survival", new SurvivalCommand($this));
        $this->getServer()->getCommandMap()->register("home", new HomeCommand($this));

        $this->playerManager = new PlayerManager($this);

        $this->saveDefaultConfig();
        $this->reloadConfig();

        $this->getServer()->getPluginManager()->registerEvents(new PlayerChangeWorldEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new DisablePluginEvent($this), $this);

        $respawn = $this->getConfig()->get("respawn-in-survival");
        if($respawn) {
            $this->getServer()->getPluginManager()->registerEvents(new RespawnEvent($this), $this);
            $this->getServer()->getPluginManager()->registerEvents(new DeathEvent($this), $this);
        }

        $this->getLogger()->info("Plugin habilitado com sucesso!");

        $this->prefix = $this->getConfig()->get("prefix", "§8[§cSurvival§8]§r");

        $this->messages = $this->getConfig()->getAll();
    }


    public function deleteFolder(string $path)
    {
        if (!is_dir($path)) return false;

        $files = array_diff(scandir($path), ['.', '..']);

        foreach ($files as $file) {
            $filePath = $path . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filePath)) {
                $this->deleteFolder($filePath);
            } else {
                unlink($filePath);
            }
        }

        return rmdir($path);
    }
}
