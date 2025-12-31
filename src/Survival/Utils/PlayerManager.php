<?php

namespace Survival\Utils;

use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class PlayerManager
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function serializeInv($items)
    {
        $serialized = [];
        $nbt = new NBT(NBT::LITTLE_ENDIAN);

        foreach ($items as $slot => $item) {

            if ($item->getId() === Item::AIR) continue;

            $data = [
                "id" => $item->getId(),
                "damage" => $item->getDamage(),
                "count" => $item->getCount(),
            ];

            if ($item->hasCompoundTag()) {
                $data["nbt"] = $nbt->write($item->getCompoundTag());
            }

            $serialized[$slot] = $data;
        }

        return $serialized;
    }

    public function deserializeInv($serialized)
    {

        $items = [];
        $nbt = new NBT(NBT::LITTLE_ENDIAN);

        foreach ($serialized as $slot => $data) {
            $item = Item::get(
                $data["id"],
                $data["damage"],
                $data["count"]
            );

            if (isset($data["nbt"])) {
                $tag = $nbt->read($data["nbt"]);

                $item->setCompoundTag($tag);
            }

            $items[$slot] = $item;
        }

        return $items;
    }

    public function savePlayerInventory($player, $level, $sendMessage)
    {

        $data = new Config($this->plugin->getDataFolder() . $level->getName() . "/playerData.yml", Config::YAML);
        
        $name = strtolower($player->getName());

        $playerData = $data->get($name, []);

        if(!isset($playerData))
            $playerData = [];

        $inv = $this->plugin->playerManager->serializeInv($player->getInventory()->getContents());
        $playerData["inv"] = $inv;
        $data->set($name, $playerData);
        $data->save();


        if($sendMessage && !empty($playerData["inv"])) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["items-saved"]);
            $player->sendMessage($message);
        }

        if($this->plugin->getConfig()->get("clear-on-quit")) $player->getInventory()->clearAll();
    }

    public function restorePlayerInventory($player, $level, $sendMessage)
    {
        $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerData.yml", Config::YAML);

        $name = strtolower($player->getName());

        $playerData = $data->get($name, []);

        if(empty($playerData) || !isset($playerData["inv"]))
            return;

        $items = $this->plugin->playerManager->deserializeInv($playerData["inv"]);

        $player->getInventory()->setContents($items);

        if($sendMessage && !empty($playerData["inv"])) {
            $message = str_replace(["{prefix}"], [$this->plugin->prefix], $this->plugin->messages["items-restored"]);
            $player->sendMessage($message);
        }
    }

    public function savePlayerPosition($player, $level) {
        $pos = [
                "pos_x" => (int)$player->getX(),
                "pos_y" => (int)$player->getY(),
                "pos_z" => (int)$player->getZ(),
                "level" => $level->getFolderName()
        ];
            
        $name = strtolower($player->getName());

        $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerData.yml", Config::YAML);

        $playerData = $data->get($name, []);

        if(empty($playerData))
            return;

        $playerData["pos"] = $pos;

        $data->set($name, $playerData);
        $data->save();
    }

    public function restorePlayerPosition($player, $level) {
        if(!$this->plugin->messages["back-to-last-position"])
            return;
        
        $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerData.yml", Config::YAML);

        $name = strtolower($player->getName());

        $playerData = $data->get($name, []);

        if(empty($playerData) || !isset($playerData["pos"]))
            return;

        $pos = $playerData["pos"];

        $x = $pos["pos_x"];
        $y = $pos["pos_y"];
        $z = $pos["pos_z"];
        $levelData = $pos["level"];

        $player->teleport(new Vector3($x, $y, $z));
    }
}
