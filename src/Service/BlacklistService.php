<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BlacklistRepository;

class BlacklistService
{
    private BlacklistRepository $blacklistRepository;
    private EntityManagerInterface $em;

    public function __construct(BlacklistRepository $blacklistRepository, EntityManagerInterface $em)
    {
        $this->blacklistRepository = $blacklistRepository;
        $this->em = $em;
    }

    public function deleteByMsisdn(string $msisdn): void
    {
        $abonne = $this->blacklistRepository->find($msisdn);

        if ($abonne) {
            $this->em->remove($abonne);
            $this->em->flush();
        }
    }
}
