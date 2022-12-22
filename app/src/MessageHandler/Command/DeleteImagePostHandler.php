<?php

declare(strict_types=1);

namespace App\MessageHandler\Command;

use App\Message\Command\DeleteImagePost;
use App\Message\Event\ImagePostDeletedEvent;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Handler\MessageSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

use function sprintf;

class DeleteImagePostHandler implements MessageSubscriberInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private MessageBusInterface $eventBus;
    private EntityManagerInterface $entityManager;
    private ImagePostRepository $imagePostRepository;

    public function __construct(
        MessageBusInterface $eventBus,
        EntityManagerInterface $entityManager,
        ImagePostRepository $imagePostRepository,
    ) {
        $this->eventBus = $eventBus;
        $this->entityManager = $entityManager;
        $this->imagePostRepository = $imagePostRepository;
    }

    public function __invoke(DeleteImagePost $deleteImagePost): void
    {
        $imagePost = $this->imagePostRepository->find($deleteImagePost->getImagePostId());

        if (!$imagePost) {
            if ($this->logger) {
                $this->logger->error(
                    sprintf("ImagePost with ID '%d' does not exist", $deleteImagePost->getImagePostId())
                );
            }

            return;
        }

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new ImagePostDeletedEvent($imagePost->getFilename()));
    }

    /**
     * @return object[]
     */
    public static function getHandledMessages(): iterable
    {
        yield DeleteImagePost::class => [
            'method' => '__invoke',
            'priority' => 10,
            //'from_transport' is useful only if message has multiple handlers, and we want to handle this message only
            // when got from specific transport
//            'from_transport' => 'async',
        ];
    }
}
