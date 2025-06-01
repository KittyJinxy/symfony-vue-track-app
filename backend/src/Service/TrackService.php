<?php

namespace App\Service;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrackService
{
    public function __construct(
        private TrackRepository $trackRepository,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    /**
     * Get all tracks with only specific fields (exact same logic as controller)
     */
    public function getAllTracks(): array
    {
        return $this->trackRepository->findAllTracks();
    }

    /**
     * Create a new track (exact same logic as controller)
     * Returns: ['success' => true, 'id' => int] or ['success' => false, 'errors' => array]
     */
    public function createTrack(array $data): array
    {
        $track = new Track();
        $track->setTitle($data['title'] ?? '');
        $track->setArtist($data['artist'] ?? '');
        $track->setDuration($data['duration'] ?? 0);
        $track->setIsrc($data['isrc'] ?? null);

        $errors = $this->validator->validate($track);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return ['success' => false, 'errors' => $errorMessages];
        }

        $this->em->persist($track);
        $this->em->flush();

        return ['success' => true, 'id' => $track->getId()];
    }

    /**
     * Update a track (exact same logic as controller)
     * Returns: ['success' => true] or ['success' => false, 'error' => string] or ['success' => false, 'errors' => array]
     */
    public function updateTrack(int $id, array $data): array
    {
        $track = $this->trackRepository->find($id);
        if (!$track) {
            return ['success' => false, 'error' => 'Track not found'];
        }

        $track->setTitle($data['title'] ?? $track->getTitle());
        $track->setArtist($data['artist'] ?? $track->getArtist());
        $track->setDuration($data['duration'] ?? $track->getDuration());
        $track->setIsrc($data['isrc'] ?? $track->getIsrc());

        $errors = $this->validator->validate($track);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return ['success' => false, 'errors' => $errorMessages];
        }

        $this->em->flush();

        return ['success' => true];
    }

    /**
     * Delete a track (exact same logic as controller)
     * Returns: ['success' => true] or ['success' => false, 'error' => string]
     */
    public function deleteTrack(int $id): array
    {
        $track = $this->trackRepository->find($id);
        if (!$track) {
            return ['success' => false, 'error' => 'Track not found'];
        }

        $this->em->remove($track);
        $this->em->flush();

        return ['success' => true];
    }
}