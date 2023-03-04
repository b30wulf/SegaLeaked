<?php

namespace SegaCore\Core\skin;

class AvatarSkin extends BaseSkin{

    protected $BASE_SIZE = 8;

    public function __construct(string $name, int $size)
    {
        parent::__construct($name, $size);
    }

    public function createImage() {
        $size = $this->BASE_SIZE;

        imagesavealpha($this->skin, true);
        $this->image = imagecreatetruecolor($size, $size);

        // face
        imagecopyresampled($this->image, $this->skin, 0, 0, 8, 8, $size, $size, 8, 8);
        // face gear
        if($this->checkHatTransparency())
            imagecopyresampled($this->image, $this->skin, 0, 0, 40, 8, $size, $size, 8, 8);
    }
}