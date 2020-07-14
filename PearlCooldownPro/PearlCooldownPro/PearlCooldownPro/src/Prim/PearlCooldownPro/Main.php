<?php

namespace Prim\PearlCooldownPro;

use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;
use pocketmine\item\EnderPearl;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {
	
	private $pcooldown;
	private $config;
	
	public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
		$this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    }
   
     public function onEnderPearl(PlayerInteractEvent $event){
        $item = $event->getItem();
		if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
        if($item instanceof EnderPearl) {
            $cooldown = $this->config->get("cooldown");
            $player = $event->getPlayer();
            if (isset($this->pcooldown[$player->getName()]) and time() - $this->pcooldown[$player->getName()] < $cooldown) {
                $event->setCancelled(true);
                $time = time() - $this->pcooldown[$player->getName()];
                $message = $this->config->get("message");
                $message = str_replace("{cooldown}", ($cooldown - $time), $message);
                $player->sendMessage(TextFormat::GOLD . $message);
            } else {
                $this->pcooldown[$player->getName()] = time();
            }
		}
        }
    }

}
