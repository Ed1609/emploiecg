<?php

namespace App\Repository;

use App\Entity\Publicity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publicity>
 */
class PublicityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publicity::class);
    }

    public function countAllPublicity(): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.status = :statut')
            ->setParameter('statut', 1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function afficherPub(int $limit, int $offset): array
    {
        $offset = max(0, $offset);
        $limit = max(1, $limit); // Assurer un nombre valide d'éléments par page
    
        return $this->createQueryBuilder('pub')
            ->where('pub.status = :statut')
            ->setParameter('statut', 1)
            ->orderBy('pub.dateMiseEnLigneAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function  publication(): array
    {
        return $this->createQueryBuilder('pub')
            ->where('pub.status = :statut')
            ->setParameter('statut', 1)
            ->orderBy('pub.dateMiseEnLigneAt', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function updatePublicitys()
    {
        $aujourdhui = new \DateTimeImmutable();
    
        // Récupérer les publicités actives
        $result = $this->createQueryBuilder('o')
            ->where('o.status = :statut')
            ->setParameter('statut', 1)
            ->orderBy('o.dateMiseEnLigneAt', 'DESC')
            ->getQuery()
            ->getResult();
    
        // Vérifier les dates d'expiration et mettre à jour le statut
        foreach ($result as $publicity) {
            if ($publicity->getDateExpirationAt() < $aujourdhui) {
                $publicity->setStatus(0); // Désactiver la publicité expirée
                $this->getEntityManager()->persist($publicity);
            }
        }
    
        $this->getEntityManager()->flush();// Appliquer les mises à jour
    
        return $result; // Retourner la liste des publicités actives
    }
    
    //    /**
    //     * @return Publicity[] Returns an array of Publicity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Publicity
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
