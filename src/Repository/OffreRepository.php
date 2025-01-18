<?php

namespace App\Repository;

use App\Entity\Offre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offre>
 */
class OffreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offre::class);
    }

    public function afficherOffres(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.statut_offre = :statut')
            ->setParameter('statut', 0)
            ->orderBy('o.date_mise_en_ligne_at', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
    public function findAllWithPagination(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function findLastNew(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.id', 'DESC')
            ->setMaxResults(3)
            ->setFirstResult(1)
            ->getQuery()
            ->getResult();
    }

    public function afficherOffresAdmin()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.date_mise_en_ligne_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    

    public function countAllProducts(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByTitle(string $title): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.titre LIKE :title')
            ->setParameter('title', '%'.$title.'%')
            ->getQuery()
            ->getResult();
    }

}
