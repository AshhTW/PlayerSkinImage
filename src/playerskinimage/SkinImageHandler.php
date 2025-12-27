<?php

declare(strict_types=1);

namespace playerskinimage;

use GdImage;
use pocketmine\entity\Skin;
use pocketmine\utils\SingletonTrait;

class SkinImageHandler{
	use SingletonTrait;

	public function __construct(){
		self::setInstance($this);
	}

	public function initialize() : void{
		$dataFolder = PlayerSkinImage::getInstance()->getDataFolder();
		$saveskinPath = $dataFolder . 'saveskin';
		$saveheadPath = $dataFolder . 'savehead';

		if(!is_dir($saveskinPath)){
			mkdir($saveskinPath, 0777, true);
		}
		if(!is_dir($saveheadPath)){
			mkdir($saveheadPath, 0777, true);
		}
	}

	public function saveSkin(Skin $skin, string $name) : void{
		$dataFolder = PlayerSkinImage::getInstance()->getDataFolder();
		$path = $dataFolder . 'saveskin';
		if(!is_dir($path)){
			mkdir($path, 0777, true);
		}

		$img = $this->skinDataToImage($skin->getSkinData());
		if($img === null){
			return;
		}

		imagepng($img, $dataFolder . 'saveskin/' . $name . '.png');
		$this->saveHead($name, $skin->getSkinData());
	}

	public function deleteSkin(string $name) : void{
		$dataFolder = PlayerSkinImage::getInstance()->getDataFolder();
		@unlink($dataFolder . 'saveskin/' . $name . '.png');
		@unlink($dataFolder . 'savehead/' . $name . '.png');
	}

	private function saveHead(string $name, string $skinData) : void{
		$dataFolder = PlayerSkinImage::getInstance()->getDataFolder();
		$size = strlen($skinData);
		$width = [64 * 32 * 4 => 64, 64 * 64 * 4 => 64, 128 * 128 * 4 => 128][$size];
		$height = [64 * 32 * 4 => 32, 64 * 64 * 4 => 64, 128 * 128 * 4 => 128][$size];
		$skinPos = 0;
		$image = imagecreatetruecolor(128, 128);
		$head = imagecreatetruecolor(64, 64);

		if($image !== false){
			imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
			if($head !== false){
				imagefill($head, 0, 0, imagecolorallocatealpha($head, 0, 0, 0, 127));
			}

			for($y = 0; $y < $height; $y++){
				for($x = 0; $x < $width; $x++){
					$r = ord($skinData[$skinPos]);
					$skinPos++;
					$g = ord($skinData[$skinPos]);
					$skinPos++;
					$b = ord($skinData[$skinPos]);
					$skinPos++;
					$a = 127 - intdiv(ord($skinData[$skinPos]), 2);
					$skinPos++;
					$col = imagecolorallocatealpha($image, $r, $g, $b, $a);

					if($width === 128 && $height === 128){
						imagesetpixel($image, $x, $y, $col);
					}else{
						imagesetpixel($image, $x * 2, $y * 2, $col);
						imagesetpixel($image, $x * 2 + 1, $y * 2, $col);
						imagesetpixel($image, $x * 2, $y * 2 + 1, $col);
						imagesetpixel($image, $x * 2 + 1, $y * 2 + 1, $col);
					}

					if($head !== false){
						if($x >= $width / 8 && $x < ($width / 8) * 2 && $y >= $height / 8 && $y < ($height / 8) * 2){
							$nheight = 64 / ($height / 8);
							$nwidth = 64 / ($width / 8);
							for($i = 0; $i < $nheight; $i++){
								for($j = 0; $j < $nwidth; $j++){
									$hx = ($x % intval($width / 8)) * $nwidth + $x % $nwidth + $j - $x % $nwidth;
									if($x % $nwidth === 0){
										$hx = ($x - intval($width / 8)) * $nwidth + $j;
									}
									$hy = ($y % intval($height / 8)) * $nheight + $y % $nheight + $i - $y % $nheight;
									if($y % $nheight === 0){
										$hy = ($y - intval($height / 8)) * $nheight + $i;
									}
									imagesetpixel($head, $hx, $hy, imagecolorallocatealpha($head, $r, $g, $b, $a));
								}
							}
						}
						if($x >= ($width / 8) * 5 && $x < ($width / 8) * 6 && $y >= $height / 8 && $y < ($height / 8) * 2){
							$nheight = 64 / ($height / 8);
							$nwidth = 64 / ($width / 8);
							for($i = 0; $i < $nheight; $i++){
								for($j = 0; $j < $nwidth; $j++){
									$hx = (($x - ($width / 8) * 5) % intval($width / 8)) * $nwidth + ($x - ($width / 8) * 5) % $nwidth + $j - $x % $nwidth;
									if($x % $nwidth === 0){
										$hx = ($x - ($width / 8) * 5) * $nwidth + $j;
									}
									$hy = ($y % intval($height / 8)) * $nheight + $y % $nheight + $i - $y % $nheight;
									if($y % $nheight === 0){
										$hy = ($y - intval($height / 8)) * $nheight + $i;
									}
									imagesetpixel($head, $hx, $hy, imagecolorallocatealpha($head, $r, $g, $b, $a));
								}
							}
						}
					}
				}
			}

			if($head !== false){
				imagesavealpha($head, true);
				imagepng($head, $dataFolder . 'savehead/' . $name . '.png');
				imagedestroy($head);
			}
		}
	}

	private function skinDataToImage(string $skinData) : ?GdImage{
		$size = strlen($skinData);

		$skin_widget_map = [
			64 * 32 * 4 => 64,
			64 * 64 * 4 => 64,
			128 * 128 * 4 => 128
		];

		$skin_height_map = [
			64 * 32 * 4 => 32,
			64 * 64 * 4 => 64,
			128 * 128 * 4 => 128
		];

		$width = $skin_widget_map[$size];
		$height = $skin_height_map[$size];
		$skinPos = 0;
		$image = imagecreatetruecolor($width, $height);
		if($image === false){
			return null;
		}

		imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
		for($y = 0; $y < $height; $y++){
			for($x = 0; $x < $width; $x++){
				$r = ord($skinData[$skinPos]);
				$skinPos++;
				$g = ord($skinData[$skinPos]);
				$skinPos++;
				$b = ord($skinData[$skinPos]);
				$skinPos++;
				$a = 127 - intdiv(ord($skinData[$skinPos]), 2);
				$skinPos++;
				$col = imagecolorallocatealpha($image, $r, $g, $b, $a);
				imagesetpixel($image, $x, $y, $col);
			}
		}

		imagesavealpha($image, true);
		return $image;
	}
}
