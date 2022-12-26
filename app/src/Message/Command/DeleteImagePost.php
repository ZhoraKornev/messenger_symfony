<?php

namespace App\Message\Command;

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