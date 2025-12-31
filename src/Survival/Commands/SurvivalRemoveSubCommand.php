<?php

namespace Survival\Commands;

use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\Player;
use SmartCommand\utils\AdminPermissionTrait;
use Survival\Main;
use SmartCommand\command\argument\TextArgument;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\rule\defaults\PermissionCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\command\subcommand\BaseSubCommand;
use SmartCommand\command\subcommand\SubCommand;


class SurvivalRemoveSubCommand extends BaseSubCommand 
{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin, SmartCommand $smartCommand)
    {
        $this->plugin = $plugin;
        parent::__construct($smartCommand, "remove", "remove the property survival from a world", ["rm"]);
    }

    use AdminPermissionTrait;
    protected static function getRuntimePermission(): string
    {
        return "survival.remove";
    }

    protected function prepare() {
        $this->registerArgument(0, new TextArgument("world", false));

        $this->argumentsDescription = "Remove the propertie survival of a world";

        $this->registerRules
        (
            new PermissionCommandRule(),
            new OnlyInGameCommandRule()
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

        $level = $args->getValue("world");

        if(is_null($level) || empty($level)) 
        {
            $level = $sender->getLevel()->getFolderName();
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

        if(!file_exists($this->plugin->getDataFolder() . $level)) {
            $messageNotFound = str_replace(["{prefix}", "{level}"], [$this->plugin->prefix, $level], $this->plugin->messages["not-found"]);
            $sender->sendMessage($messageNotFound);
            return;
        }

        $this->plugin->deleteFolder($this->plugin->getDataFolder() . $level);

        $messageRemoved = str_replace(["{prefix}", "{level}"], [$this->plugin->prefix, $level], $this->plugin->messages["removed-survival"]);
 
        $sender->sendMessage($messageRemoved);

        return;
    }
}