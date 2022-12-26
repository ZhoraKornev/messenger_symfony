<?php

namespace App\MessageHandler;

use App\Message\DeletePhotoFile;
use App\Photo\PhotoFileManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeletePhotoFileHandler implements MessageHandlerInterface
{

    public function __construct(private PhotoFileManager $photoFileManager)
    {
    }

    public function __invoke(DeletePhotoFile $deletePhotoFile)
    {
        $this->photoFileManager->deleteImage($deletePhotoFile->getFilename());
    }
}