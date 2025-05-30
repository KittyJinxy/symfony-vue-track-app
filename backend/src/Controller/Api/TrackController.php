<?php

namespace App\Controller\Api;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/tracks', name: 'api_tracks_')]
class TrackController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(TrackRepository $trackRepository): JsonResponse
    {
        $tracks = $trackRepository->findAll();

        $data = array_map(function (Track $track) {
            return [
                'id' => $track->getId(),
                'title' => $track->getTitle(),
                'artist' => $track->getArtist(),
                'duration' => $track->getDuration(),
                'isrc' => $track->getIsrc(),
            ];
        }, $tracks);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $track = new Track();
        $track->setTitle($data['title'] ?? '');
        $track->setArtist($data['artist'] ?? '');
        $track->setDuration($data['duration'] ?? 0);
        $track->setIsrc($data['isrc'] ?? null);

        $errors = $validator->validate($track);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($track);
        $em->flush();

        return $this->json([
            'id' => $track->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        TrackRepository $repository,
        ValidatorInterface $validator
    ): JsonResponse {
        $track = $repository->find($id);
        if (!$track) {
            return $this->json(['error' => 'Track not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $track->setTitle($data['title'] ?? $track->getTitle());
        $track->setArtist($data['artist'] ?? $track->getArtist());
        $track->setDuration($data['duration'] ?? $track->getDuration());
        $track->setIsrc($data['isrc'] ?? $track->getIsrc());

        $errors = $validator->validate($track);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return $this->json(['message' => 'Track updated']);
    }
}
