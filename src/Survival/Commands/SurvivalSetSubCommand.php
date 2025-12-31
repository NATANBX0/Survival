<?php

namespace Survival\Commands;

use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use SmartCommand\command\argument\TextArgument;
use SmartCommand\utils\AdminPermissionTrait;
use Survival\Main;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\rule\defaults\PermissionCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;

class SurvivalSetSubCommand extends BaseSubCommand
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin, SmartCommand $smartCommand)
    {
        $this->plugin = $plugin;
        parent::__construct($smartCommand, "set", "set the property survival to a world");
    }
    
    use AdminPermissionTrait;
    protected static function getRuntimePermission(): string
    {
        return "survival.set";
    }

    protected function prepare()
    {
        $this->registerArgument(0, new TextArgument("world", false));

        $this->argumentsDescription = "Set a Survival world";

        $this->registerRules
        (
            new OnlyInGameCommandRule(), 
            new PermissionCommandRule()
        );
    }


    /**
     * Summary of onRun
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string $subcommandLabel
     * @param CommandArguments $args
     * @return void
     */
    protected function onRun(CommandSender $sender, string $commandLabel, string $subcommandLabel, CommandArguments $args)
    {
        /**
         * @var Player $sender
         */
        $player = $sender;

        $level = $args->getValue("world");

        if(is_null($level) || empty($level)) 
        {
            $level = $player->getLevel()->getFolderName();
        } 
        else if($this->plugin->getServer()->getLevelByName($level) instanceof Level) 
        {
            $level = $this->plugin->getServer()->getLevelByName($level)->getFolderName();
        } 
        else 
        {
            $message = str_replace(["{prefix}", "{level}"], [$this->plugin->prefix, $level], $this->plugin->messages["not-found"]);
            $sender->sendMessage($message);
            return;
        }

        $path = $this->plugin->getDataFolder() . $level . "/"; 
        
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
        }

        $message = str_replace(["{prefix}", "{level}"], [$this->plugin->prefix, $level], $this->plugin->messages["set-survival"]);

        $player->sendMessage($message);
        return;
    }
}
