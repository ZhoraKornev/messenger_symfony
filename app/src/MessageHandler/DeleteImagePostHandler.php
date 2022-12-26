<?php

namespace App\MessageHandler;

use App\Message\DeleteImagePost;
use App\Message\DeletePhotoFile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteImagePostHandler implements MessageHandlerInterface
{
    public function __construct(
        private MessageBusInterface    $messageBus,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function __invoke(DeleteImagePost $deleteImagePost)
    {
        $imagePost = $deleteImagePost->getImagePost();
        $fileName = $imagePost->getFilename();

        $this->entityManager->remove($imagePost);
        $this->entityManager->flush();

        $this->messageBus->dispatch(new DeletePhotoFile($fileName));
    }
}