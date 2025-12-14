<?php

declare(strict_types=1);

namespace Survival\Commands;

use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use SmartCommand\command\argument\TextArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\utils\MemberPermissionTrait;
use Survival\Main;

class SetHomeCommand extends BaseSubCommand {
    private $plugin;

    use MemberPermissionTrait;

    public function __construct(Main $plugin, SmartCommand $smartCommand)
    {
        $this->plugin = $plugin;
        parent::__construct($smartCommand, "set", "set a home", ["set", "add"]);
    }


    protected function prepare()
    {
        $this->registerArgument(0, new TextArgument("home"));

        $this->registerRule(new OnlyInGameCommandRule());
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $level = $sender->getLevel();

        $homeName = $args->getString("home");

        $pos = [
            "pos_x" => (int)$sender->getX(),
            "pos_y" => (int)$sender->gety(),
            "pos_z" => (int)$sender->getz(),
            "level" => $sender->getLevel()->getFolderName()
        ];

        if(!is_dir($this->plugin->getDataFolder() . $level->getFolderName())) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["home-block"]);
            $sender->sendMessage($message);
            return;
        }

        $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerData.yml", Config::YAML);

        $name = strtolower($sender->getName());

        $playerData = $data->get($name);

        if(!isset($playerData["home"]))
            $playerData["home"] = [];

        if(array_key_exists($homeName, $playerData["home"])) {
            $message = str_replace(["{prefix}", "{home}"], [$this->plugin->prefix, $homeName], $this->plugin->messages["home-with-the-same-name"]);
            $sender->sendMessage($message);
            return;
        }

        $playerData["home"][$homeName] = $pos;
        $data->set($name, $playerData);
        $data->save();

        $message = str_replace(["{prefix}", "{home}"], [$this->plugin->prefix, $homeName], $this->plugin->messages["home-added"]);

        $sender->sendMessage($message);
    }
    
}