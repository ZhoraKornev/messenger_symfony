<?php

namespace App\Message\Command;

class LogEmoji
{

    public function __construct( private int $emojiIndex)
    {
    }

    /**
     * @return int
     */
    public function getEmojiIndex(): int
    {
        return $this->emojiIndex;
    }
}
