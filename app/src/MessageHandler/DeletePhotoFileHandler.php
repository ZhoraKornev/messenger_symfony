<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\DeletePhotoFile;
use App\Photo\PhotoFileManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeletePhotoFileHandler implements MessageHandlerInterface
{
    public function __construct(private PhotoFileManager $photoManager)
    {
    }

    public function __invoke(DeletePhotoFile $deletePhotoFile): void
    {
        $this->photoManager->deleteImage($deletePhotoFile->getFilename());
    }
}
