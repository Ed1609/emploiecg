<?php

namespace App\Entity;

use App\Repository\BlacklistRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: BlacklistRepository::class)]
#[Broadcast]
class Blacklist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $MSISDN = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateAjout = null;

    #[ORM\Column(length: 255)]
    private ?string $specialite = null;

    #[ORM\Column(length: 100)]
    private ?string $ville = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMSISDN(): ?string
    {
        return $this->MSISDN;
    }

    public function setMSISDN(string $MSISDN): static
    {
        $this->MSISDN = $MSISDN;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->DateAjout;
    }

    public function setDateAjout(\DateTimeInterface $DateAjout): static
    {
        $this->DateAjout = $DateAjout;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }
}
