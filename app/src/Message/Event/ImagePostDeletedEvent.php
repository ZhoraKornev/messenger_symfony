<?php

declare(strict_types=1);

namespace App\Message\Event;

class ImagePostDeletedEvent
{
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
