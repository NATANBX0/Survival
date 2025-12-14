<?php

namespace Survival\Commands;

use SmartCommand\command\rule\defaults\PermissionCommandRule;
use SmartCommand\utils\AdminPermissionTrait;
use Survival\Main;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;

class SurvivalListSubCommand extends BaseSubCommand {

    /**
     * Summary of plugin
     * @var Main $plugin
     */
    private $plugin;

    public function __construct(Main $plugin, SmartCommand $smartCommand)
    {
        $this->plugin = $plugin;
        parent::__construct($smartCommand, "list", "List every world with the survival property");
    }

    use AdminPermissionTrait;
    protected static function getRuntimePermission(): string
    {
        return "survival.list";
    }

    protected function prepare()
    {
        $this->registerRule(new PermissionCommandRule());
    }

    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        $path = $this->plugin->getDataFolder();
        $worldFolders = array_filter(glob($path . "*"), 'is_dir');
        $worlds = [];

        foreach($worldFolders as $world)
            $worlds[] = basename($world);

        if(empty($worlds)) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["no-survival-worlds"]);
            $sender->sendMessage($message);
            return;
        }

        $survivalWorlds = implode(", ", $worlds);

        $message = str_replace(["{prefix}", "{list}"], [$this->plugin->prefix, $survivalWorlds], $this->plugin->messages["survival-list"]);

        $sender->sendMessage($message);
        
        return;
    }
}