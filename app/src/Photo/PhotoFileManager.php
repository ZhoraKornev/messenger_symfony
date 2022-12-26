<?php

declare(strict_types=1);

namespace App\Photo;

use App\Entity\ImagePost;
use Exception;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function fclose;
use function fopen;
use function is_resource;
use function pathinfo;
use function sprintf;
use function uniqid;

use const PATHINFO_FILENAME;

class PhotoFileManager
{
    public function __construct(
        private FilesystemInterface $photoFilesystem,
        private string $publicAssetBaseUrl
    ){
    }

    /**
     * @throws FileExistsException
     * @throws Exception
     */
    public function uploadImage(File $file): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = pathinfo($originalFilename, PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file->guessExtension();
        $stream = fopen($file->getPathname(), 'r');
        $result = $this->photoFilesystem->writeStream(
            $newFilename,
            $stream,
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
            ]
        );

        if ($result === false) {
            throw new Exception(sprintf('Could not write uploaded file "%s"', $newFilename));
        }

        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFilename;
    }

    /**
     * @throws FileNotFoundException
     */
    public function deleteImage(string $filename): void
    {
        $this->photoFilesystem->delete($filename);
    }

    public function getPublicPath(ImagePost $imagePost): string
    {
        return $this->publicAssetBaseUrl . '/' . $imagePost->getFilename();
    }

    /**
     * @throws FileNotFoundException
     */
    public function read(string $filename): string
    {
        return $this->photoFilesystem->read($filename);
    }

    /**
     * @throws FileNotFoundException
     */
    public function update(string $filename, string $updatedContents): void
    {
        $this->photoFilesystem->update($filename, $updatedContents);
    }
}
