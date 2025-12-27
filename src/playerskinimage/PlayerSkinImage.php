<?php

declare(strict_types=1);

namespace playerskinimage;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class PlayerSkinImage extends PluginBase{
	use SingletonTrait;

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		$this->saveDefaultConfig();
		new SkinImageHandler();
		SkinImageHandler::getInstance()->initialize();
		$this->getServer()->getPluginManager()->registerEvents(new PlayerSkinImageListener(), $this);
	}

	public function isSavingEnabledFor(Player $player) : bool{
		$config = $this->getConfig();
		return (bool) $config->get('default-enabled', true);
	}
}
