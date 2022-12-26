<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ImagePost;
use App\Message\Command\AddPonkaToImage;
use App\Message\Command\DeleteImagePost;
use App\Photo\PhotoFileManager;
use App\Repository\ImagePostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function count;

class ImagePostController extends AbstractController
{
    #[Route('/api/images', methods: ['GET'])]
    public function list(ImagePostRepository $repository): JsonResponse
    {
        $posts = $repository->findBy([], ['createdAt' => 'DESC']);

        return $this->toJson(['items' => $posts]);
    }

    #[Route('/api/images', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        PhotoFileManager $photoManager,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus
    ): JsonResponse {
        /** @var UploadedFile $imageFile */
        $imageFile = $request->files->get('file');

        $errors = $validator->validate($imageFile, [
            new Image(),
            new NotBlank(),
        ]);

        if (count($errors) > 0) {
            return $this->toJson($errors, Response::HTTP_BAD_REQUEST);
        }

        $newFilename = $photoManager->uploadImage($imageFile);
        $imagePost = new ImagePost();
        $imagePost->setFilename($newFilename);
        $imagePost->setOriginalFilename($imageFile->getClientOriginalName());

        $entityManager->persist($imagePost);
        $entityManager->flush();

        $message = new AddPonkaToImage($imagePost->getId());
        $envelope = new Envelope($message, [new DelayStamp(500)]);

        $messageBus->dispatch($envelope);

        return $this->toJson($imagePost, Response::HTTP_CREATED);
    }

    #[Route('/api/images/{id}', methods: ['DELETE'])]
    public function delete(
        ImagePost $imagePost,
        MessageBusInterface $messageBus
    ): Response {
        $message = new DeleteImagePost($imagePost->getId());
        $messageBus->dispatch($message);
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/images/{id}', name: 'get_image_post_item', methods: ['GET'])]
    public function getItem(ImagePost $imagePost): JsonResponse
    {
        return $this->toJson($imagePost);
    }

    private function toJson($data, int $status = Response::HTTP_OK, array $headers = [], array $context = []): JsonResponse
    {
        // add the image:output group by default
        if (!isset($context['groups'])) {
            $context['groups'] = ['image:output'];
        }

        return $this->json($data, $status, $headers, $context);
    }
}
