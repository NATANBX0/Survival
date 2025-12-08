<?php

namespace Survival\Utils;

use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\utils\Config;

class InventoryManager {

    private $plugin;

    Public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function serializeInv($items) {
        $serialized = [];
        $nbt = new NBT(NBT::LITTLE_ENDIAN);

        foreach($items as $slot => $item) {
            
            if($item->getId() === Item::AIR) continue;

            $data = [
                "id" => $item->getId(),
                "damage" => $item->getDamage(),
                "count" => $item->getCount(),
            ];

            if($item->hasCompoundTag()) {
                $data["nbt"] = $nbt->write($item->getCompoundTag());
            }

            $serialized[$slot] = $data;
        }

        return $serialized;
    }

    public function deserializeInv($serialized) {
        
        $items = [];
        $nbt = new NBT(NBT::LITTLE_ENDIAN);

        foreach($serialized as $slot => $data) {
            $item = Item::get($data["id"],
            $data["damage"],
            $data["count"]);

            if(isset($data["nbt"])) {
                $tag = $nbt->read($data["nbt"]);

                $item->setCompoundTag($tag);
            }

            $items[$slot] = $item;
        }

        return $items;
    }


        public function savePlayerInventory($player, $level) {
        
        $data = new Config($this->plugin->getDataFolder() . $level->getName() . "/playerItems.yml", Config::YAML);

        $name = strtolower($player->getName());

        $inv = $this->plugin->inventoryManager->serializeInv($player->getInventory()->getContents());
        $data->set($name, ["inv" => $inv]);
        $data->save();

        $player->sendMessage($this->plugin->prefix . $this->plugin->messages["items-saved"]);
    }

    public function restorePlayerInventory($player, $level) {
        $data = new Config($this->plugin->getDataFolder() . $level->getFolderName() . "/playerItems.yml", Config::YAML);

        $name = strtolower($player->getName());

        $playerData = $data->get($name);

        $items = $this->plugin->inventoryManager->deserializeInv($playerData["inv"]);
        
        $player->getInventory()->setContents($items);

        $player->sendMessage($this->plugin->prefix . $this->plugin->messages["items-restored"]);
    }
}