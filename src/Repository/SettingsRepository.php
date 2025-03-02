<?php

namespace App\Repository;

use App\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Settings>
 */
class SettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    // Vous pouvez ajouter des méthodes personnalisées ici

    /**
     * Exemple de méthode pour trouver tous les paramètres où l'identifiant correspond à une valeur donnée.
     */
    public function findByIdentifiant(string $identifiant): ?Settings
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.identifiant = :identifiant')
            ->setParameter('identifiant', $identifiant)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Exemple de méthode pour récupérer tous les paramètres.
     */
    public function findAllSettings(): array
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->getResult();
    }

    /**
     * Exemple de méthode pour récupérer les paramètres par leur secteur d'activité.
     */
    public function findBySecteurActivite(string $secteur): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.secteurActivite = :secteur')
            ->setParameter('secteur', $secteur)
            ->getQuery()
            ->getResult();
    }
}
