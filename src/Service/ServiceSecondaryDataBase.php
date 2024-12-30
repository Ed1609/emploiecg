<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class ServiceSecondaryDataBase
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $secondaryEntityManager)
    {
        $this->entityManager = $secondaryEntityManager;
    }

    public function getDataFromSecondaryDb(): array
    {
        $connection = $this->entityManager->getConnection();
        $sql = 'SELECT * FROM Clients';
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative();
    }
}
