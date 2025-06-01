<?php

namespace App\Controller\Api;

use App\Service\TrackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/tracks', name: 'api_tracks_')]
class TrackController extends AbstractController
{
    public function __construct(
        private TrackService $trackService
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $tracks = $this->trackService->getAllTracks();
        return $this->json($tracks);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $result = $this->trackService->createTrack($data);

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return $this->json(['errors' => $result['errors']], Response::HTTP_BAD_REQUEST);
            }
            return $this->json(['error' => $result['error']], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['id' => $result['id']], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $result = $this->trackService->updateTrack($id, $data);

        if (!$result['success']) {
            if (isset($result['error'])) {
                return $this->json(['error' => $result['error']], Response::HTTP_NOT_FOUND);
            }
            if (isset($result['errors'])) {
                return $this->json(['errors' => $result['errors']], Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->json(['message' => 'Track updated']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $result = $this->trackService->deleteTrack($id);

        if (!$result['success']) {
            return $this->json(['error' => $result['error']], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Track deleted'], Response::HTTP_OK);
    }
}