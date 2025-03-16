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

    public function getAbonneStats(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT ville, COUNT(id) as total 
            FROM abonne 
            GROUP BY ville
            ORDER BY total DESC
        ";

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery()->fetchAllAssociative();

        // Nombre total d'abonnés
        $totalAbonnes = array_sum(array_column($resultSet, 'total'));

        // Calcul du poids de chaque ville
        foreach ($resultSet as &$row) {
            $row['pourcentage'] = ($totalAbonnes > 0) ? round(($row['total'] / $totalAbonnes) * 100, 2) : 0;
        }

        return [
            'total_abonnes' => $totalAbonnes,
            'stats_par_ville' => $resultSet
        ];
    }

    public function userParVille(string $ville,int $limit, int $offset): array
    {
        $offset = max(0, $offset);
        return $this->createQueryBuilder('o')
            ->where('o.Ville LIKE :ville')
            ->setParameter('ville', '%'.$ville.'%')
            ->orderBy('o.createAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)            
            ->getQuery()
            ->getResult();
    }
}
