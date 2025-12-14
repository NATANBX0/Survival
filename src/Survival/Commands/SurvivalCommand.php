<?php

namespace Survival\Commands;

use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use SmartCommand\utils\AdminPermissionTrait;
use Survival\Main;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\rule\defaults\PermissionCommandRule;
use SmartCommand\command\SmartCommand;
use Survival\Commands\SurvivalSetSubCommand;
use Survival\Commands\SurvivalListSubCommand;
use Survival\Commands\SurvivalRemoveSubCommand;

class SurvivalCommand extends SmartCommand
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("survival", "Survival World manager");
    }
    
    use AdminPermissionTrait;
    protected static function getRuntimePermission(): string
    {
        return "survival.set";
    }

    protected function prepare()
    {
        $this->registerSubCommands
        ([
            new SurvivalSetSubCommand($this->plugin, $this),
            new SurvivalRemoveSubCommand($this->plugin, $this),
            new SurvivalListSubCommand($this->plugin, $this)
        ]);

        $this->argumentsDescription = "Set a Survival world";

        $this->registerRules
        (
            new OnlyInGameCommandRule(), 
            new PermissionCommandRule()
        );

        // TODO: For some reason this dont work solve in the future
        //$this->setPrefix($this->plugin->prefix);
    }

    /**
     * Summary of onRun
     * @param CommandSender $sender
     * @param string $label
     * @param CommandArguments $args
     * @return void
     */
    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        foreach($this->generateSubCommandsUsages("survival", $sender) as $subcmd)
            $sender->sendMessage($this->plugin->prefix . " " . $subcmd);
    }
}
