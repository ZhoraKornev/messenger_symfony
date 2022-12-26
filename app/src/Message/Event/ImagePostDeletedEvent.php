<?php

namespace App\Message\Event;

class ImagePostDeletedEvent
{
    public function __construct(private string $fileName)
    {
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
