<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Reservations en_cours dont la date de fin est dépassée (patients en retard de sortie).
     */
    public function findOverdue(): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.statut = :statut')
            ->andWhere('r.dateFin < :now')
            ->setParameter('statut', 'en_cours')
            ->setParameter('now', new \DateTime())
            ->orderBy('r.dateFin', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Reservations dont la sortie est prévue aujourd'hui (et toujours en_cours).
     */
    public function findEndingToday(): array
    {
        $now     = new \DateTime();
        $endOfDay = (clone $now)->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            ->where('r.statut = :statut')
            ->andWhere('r.dateFin BETWEEN :now AND :endOfDay')
            ->setParameter('statut', 'en_cours')
            ->setParameter('now', $now)
            ->setParameter('endOfDay', $endOfDay)
            ->orderBy('r.dateFin', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    /**
     * Admissions (dateDebut) survenues aujourd'hui.
     */
    public function findAdmittedToday(): array
    {
        $startOfDay = (new \DateTime())->setTime(0, 0, 0);
        $now        = new \DateTime();

        return $this->createQueryBuilder('r')
            ->where('r.dateDebut BETWEEN :startOfDay AND :now')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('now', $now)
            ->orderBy('r.dateDebut', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
