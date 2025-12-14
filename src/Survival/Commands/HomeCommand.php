<?php

declare(strict_types=1);

namespace Survival\Commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use SmartCommand\command\argument\TextArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\utils\MemberPermissionTrait;

class HomeCommand extends SmartCommand {

    private $plugin;

    use MemberPermissionTrait;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
        return parent::__construct("home", "teleport to a home");
    }

    protected function prepare()
    {
        $this->registerArgument(0, new TextArgument("home", false));

        $this->registerSubCommands
        ([
            new SetHomeCommand($this->plugin, $this),
            new DelHomeCommand($this->plugin, $this)
        ]);

        $this->registerRule(new OnlyInGameCommandRule());

        // TODO: For some reason this dont work solve in the future
        //$this->setPrefix($this->plugin->prefix);
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $homeName = $args->getValue("home");

        $level = $sender->getLevel();

        if(!is_dir($this->plugin->getDataFolder() . $level->getFolderName())) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["home-block"]);
            $sender->sendMessage($message);
            return;
        }
        
        $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerData.yml", Config::YAML);
        
        $name = strtolower($sender->getName());
        
        $playerData = $data->get($name);


        if(is_null($homeName)) {
            
            $homes = [];

            $dataHomes = $playerData["home"];
            
            if(!isset($playerData["home"])) {
                $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["no-home"]);
            }
            else {
                foreach(array_keys($dataHomes) as $home) {
                    $homes[] = $home;
                }
                $homes = implode(", ", $homes);
                $message = str_replace(["{prefix}", "{homes}"], [$this->plugin->prefix, $homes], $this->plugin->messages["home-list"]);
            }
            $sender->sendMessage("§7--------------- " . $this->plugin->prefix . " §7---------------");
            foreach($this->generateUsageList("Home", $sender) as $subCommands)
                $sender->sendMessage($this->plugin->prefix . " " . $subCommands);
            $sender->sendMessage($message . "\n§7----------------------------------------");
            return;
        }
        
        if(!isset($playerData["home"])) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["no-home"]);
            $sender->sendMessage($message);
            return;
        }

        if(!array_key_exists($homeName, $playerData["home"])) {
            $message = str_replace(["{prefix}", "{home}"], [$this->plugin->prefix, $homeName], $this->plugin->messages["no-home-name-registered"]);
            $sender->sendMessage($message);
            return;
        }

        $x = $playerData["home"][$homeName]["pos_x"];
        $y = $playerData["home"][$homeName]["pos_y"];
        $z = $playerData["home"][$homeName]["pos_z"];
        $level = $playerData["home"][$homeName]["level"];

        $level = $this->plugin->getServer()->getLevelByName($level);
        
        $sender->teleport(new Position($x, $y, $z, $level));
        $message = str_replace(["{prefix}", "{home}"], [$this->plugin->prefix, $homeName], $this->plugin->messages["teleported-to-home"]);
        $sender->sendMessage($message);
    }
}