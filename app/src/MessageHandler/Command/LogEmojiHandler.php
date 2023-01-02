<?php

namespace App\MessageHandler\Command;

use App\Message\Command\LogEmoji;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class LogEmojiHandler implements MessageHandlerInterface
{
    private static $emojis = [
        '🐱',
        '🕣',
        '👉',
        '🤑',
        '😣',
        '🦌',
        '😍',
        '😑',
    ];

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(LogEmoji $logEmoji)
    {
        $index = $logEmoji->getEmojiIndex();
        $emoji = self::$emojis[$index] ?? self::$emojis[0];

        $this->logger->info('Important message ' . $emoji);
     }

}