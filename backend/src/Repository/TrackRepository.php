<?php

namespace App\Repository;

use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Track>
 */
class TrackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Track::class);
    }

    /**
     * @return Track[] Returns an array of Track objects
     */
    public function findAllTracks(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.id', 't.title', 't.artist', 't.duration', 't.isrc')
            ->getQuery()
            ->getArrayResult();
    }
  
}
