<?php

namespace App\Repository;

use App\Entity\Abonne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Abonne>
 */
class AbonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonne::class);
    }

    public function countAllAbonnes(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where("o.roles LIKE :roles")
            ->setParameter('roles', '%ROLE_USER%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function afficherAbonnes(int $limit, int $offset): array
    {
        $offset = max(0, $offset);
        
        return $this->createQueryBuilder('o')
            ->where("o.roles LIKE :roles")
            ->setParameter('roles', '%ROLE_USER%')
            ->orderBy('o.createAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}
