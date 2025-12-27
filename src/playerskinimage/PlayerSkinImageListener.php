<?php

declare(strict_types=1);

namespace playerskinimage;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerSkinImageListener implements Listener{
	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		if(!PlayerSkinImage::getInstance()->isSavingEnabledFor($player)){
			return;
		}
		SkinImageHandler::getInstance()->saveSkin($player->getSkin(), $player->getName());
	}

	public function onChangeSkin(PlayerChangeSkinEvent $event) : void{
		$player = $event->getPlayer();
		if(!PlayerSkinImage::getInstance()->isSavingEnabledFor($player)){
			return;
		}
		SkinImageHandler::getInstance()->saveSkin($event->getNewSkin(), $player->getName());
	}
}
