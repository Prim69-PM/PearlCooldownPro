<?php

namespace Prim\PearlCooldownPro;

use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\item\EnderPearl;
use pocketmine\event\Listener;
use function str_replace;
use function time;

class Main extends PluginBase implements Listener {

	public int $cooldown;
	public string $message;

	public array $cooldowns = [];

	public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->cooldown = $this->getConfig()->get('cooldown');
		$this->message = $this->getConfig()->get('message');
    }

	public function onItemUse(PlayerItemUseEvent $event) : void {
		if($event->getItem() instanceof EnderPearl){
			$player = $event->getPlayer();
			$cd = $this->cooldowns[$player->getId()] ?? null;
			if($cd !== null && time() - $cd < $this->cooldown){
				$event->cancel();
				$player->sendMessage(str_replace('{cooldown}', $this->cooldown - (time() - $cd), $this->message));
			} else {
				$this->cooldowns[$player->getId()] = time();
			}
		}
	}

	public function onQuit(PlayerQuitEvent $event) : void {
		unset($this->cooldowns[$event->getPlayer()->getId()]);
	}

}
