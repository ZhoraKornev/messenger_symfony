<?php

declare(strict_types=1);

namespace App\Messenger;

use App\Stamp\UniqueIdStamp;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Stamp\SentStamp;

class AuditMiddleware implements MiddlewareInterface
{

    public function __construct(private LoggerInterface $messengerAuditLogger)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->last(UniqueIdStamp::class) === null) {
            $envelope = $envelope->with(new UniqueIdStamp());
        }

        /** @var UniqueIdStamp $stamp */
        $stamp = $envelope->last(UniqueIdStamp::class);

        $context = [
          'id' => $stamp->getUniqueId(),
          'class' => get_class($envelope->getMessage()),
        ];
        $envelope = $stack->next()->handle($envelope,$stack);

        if ($envelope->last(ReceivedStamp::class)) {
            $this->messengerAuditLogger->info('[{id}] Received & handling {class}', $context);
        } elseif ($envelope->last(SentStamp::class)) {
            $this->messengerAuditLogger->info('[{id}] sent {class}', $context);
        } else {
            $this->messengerAuditLogger->info('[{id}] Handling or sending {class}', $context);
        }


        return $stack->next()->handle($envelope, $stack);
    }
}
