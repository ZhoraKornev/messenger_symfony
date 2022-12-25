<?php

namespace App\Message;

use App\Entity\ImagePost;

class AddPonkaToImage
{

    public function __construct(private ImagePost $imagePost)
    {
    }

    /**
     * @return ImagePost
     */
    public function getImagePost(): ImagePost
    {
        return $this->imagePost;
    }
}