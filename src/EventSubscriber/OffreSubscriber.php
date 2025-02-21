<?php

namespace App\EventSubscriber;

use App\Entity\Offre;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;

class OffreSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Offre) {
            return;
        }

        // Vérifier si l'offre est expirée et mettre à jour le statut
        $aujourdhui = new \DateTimeImmutable();
        if ($entity->getDateExpirationAt() < $aujourdhui) {
            $entity->setStatutOffre(1);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Offre) {
            return;
        }

        // Vérifier si l'offre est expirée et mettre à jour le statut
        $aujourdhui = new \DateTimeImmutable();
        if ($entity->getDateExpirationAt() < $aujourdhui) {
            $entity->setStatutOffre(31);
        }
    }
}
