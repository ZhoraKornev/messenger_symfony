<?php

namespace App\Message;

use App\Entity\ImagePost;

class DeleteImagePost
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