<?php

namespace SegaCore\Core\skin;

use SegaCore\Core\Main;

class BaseSkin{
    protected $CACHE;
    protected $BASE_URL;

    protected $name;         // player's name
    protected $size;         // image height
    protected $url;          // base skin url

    protected $skin;         // skin source
    protected $lastModified; // cache control
    protected $image;        // final image


    public function __construct(string $name, int $size){
        $this->name = $name;
        $this->size = $size;
        $this->url = Main::getInstance()->getDataFolder(). "saveskin/" . $name . "png";
        $this->cache = Main::getInstance()->getDataFolder() . "saveskin/";
    }

    public function loadSkin() {
        if($this->name === NULL)
            return false;

        $path = $this->CACHE . "avatar".$this->name . ".png";

        if(!file_exists($path)) {
            $this->skin = @imagecreatefrompng($this->url);

            if($this->skin === false)
                return false;

            imagesavealpha($this->skin, true);
            imagepng($this->skin, $path);
            $this->lastModified = time();
        } else {
            $this->skin = @imagecreatefrompng($path);
            $this->lastModified = filemtime($path);
        }
        return true;
    }

    protected function checkHatTransparency()
    {
        for ($i = 0; $i < 8; $i++) {
            for ($j = 0; $j < 8; $j++) {
                $rgb = imagecolorsforindex($this->skin, imagecolorat($this->skin, 40 + $j, 8 + $i));
                if ($rgb["alpha"] == 127) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function show() {
        $skinExists = $this->loadSkin();

        if($skinExists) {
            $this->createImage();


            if($this->BASE_SIZE != $this->size)
                $this->resize();

            $this->output();

            return true;
        } else {
            return false;
        }
    }

    protected function output() {
        header("Content-type: image/png");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", $this->lastModified) . " GMT");

        imagepng($this->image);
        imagedestroy($this->image);
        imagedestroy($this->skin);
    }

    /**
     * Resize method
     */
    protected function resize() {
        $imgResized = imagecreatetruecolor($this->size, $this->size);
        imagecopyresampled($imgResized, $this->image, 0, 0, 0, 0, $this->size, $this->size, $this->BASE_SIZE, $this->BASE_SIZE);
        imagedestroy($this->image);
        $this->image = $imgResized;
    }
}