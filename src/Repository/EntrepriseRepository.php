<?php

namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entreprise>
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

    public function afficherEntrepriseAdmin()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.date_mise_en_ligne_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countAllEnterprises(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findEntrepriseById(int $id): ?Entreprise
    {
        return $this->find($id);
    }
    public function afficherEntreprise(int $limit, int $offset): array
    {
        $offset = max(0, $offset);
        
        return $this->createQueryBuilder('o')
            ->orderBy('o.date_mise_en_ligne_at', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}
