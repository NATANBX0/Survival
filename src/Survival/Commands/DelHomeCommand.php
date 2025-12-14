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

class DelHomeCommand extends BaseSubCommand {

    private $plugin;

    use MemberPermissionTrait;

    public function __construct(Main $plugin, SmartCommand $smartCommand)
    {
        $this->plugin = $plugin;
        parent::__construct($smartCommand, "delete", "delete a home", ["del", "delete", "remove", "rm"]);
    }


    protected function prepare()
    {
        $this->registerArgument(0, new TextArgument("home"));

        $this->registerRule(new OnlyInGameCommandRule());
    }


    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $homeName = $args->getString("home");

        $name = strtolower($sender->getName());

        $level = $sender->getLevel();

        if(!is_dir($this->plugin->getDataFolder() . $level->getFolderName())) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["home-block"]);
            $sender->sendMessage($message);
            return;
        }

        $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerData.yml", Config::YAML);

        $playerData = $data->get($name, []);

        $homes = $playerData["home"];

        if(!isset($homes) || !is_array($homes)) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["no-home-to-del"]);
            $sender->sendMessage($message);
            return;
        }

        if(!array_key_exists($homeName, $homes)) {
            $message = str_replace(["{prefix}", "{home}"], [$this->plugin->prefix, $homeName], $this->plugin->messages["no-home-with-this-name"]);
            $sender->sendMessage($message);
            return;
        }

        unset($homes[$homeName]);

        if(empty($homes))
            unset($homes);

        $playerData["home"] = $homes;

        $data->set($name, $playerData);
        $data->save();

        $message = str_replace(["{prefix}", "{home}"], [$this->plugin->prefix, $homeName], $this->plugin->messages["home-deleted"]);

        $sender->sendMessage($message);
    }
}