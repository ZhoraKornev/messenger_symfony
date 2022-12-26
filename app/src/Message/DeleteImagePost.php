<?php

namespace App\Message;

use App\Entity\ImagePost;

class DeleteImagePost
{

    public function __construct(private int $imagePostId)
    {
    }

    public function getImagePostId(): int
    {
        return $this->imagePostId;
    }
}