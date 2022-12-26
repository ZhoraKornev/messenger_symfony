<?php

namespace App\Middleware;

use Symfony\Component\Messenger\Stamp\StampInterface;

class UniqueIdStamp implements StampInterface
{


    public function __construct(private string $uniqueId)
    {
        $this->uniqueId = uniqid();
    }

    /**
     * @return string
     */
    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }
}