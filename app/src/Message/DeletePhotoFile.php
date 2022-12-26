<?php

namespace App\Message;

class DeletePhotoFile
{
    public function __construct(private string $filename)
    {
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}