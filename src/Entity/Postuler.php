<?php

namespace App\Entity;

use App\Repository\PostulerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostulerRepository::class)]
class Postuler
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_abonne = null;

    #[ORM\Column]
    private ?int $id_offre = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_postuler_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAbonne(): ?int
    {
        return $this->id_abonne;
    }

    public function setIdAbonne(int $id_abonne): static
    {
        $this->id_abonne = $id_abonne;

        return $this;
    }

    public function getIdOffre(): ?int
    {
        return $this->id_offre;
    }

    public function setIdOffre(int $id_offre): static
    {
        $this->id_offre = $id_offre;

        return $this;
    }

    public function getDatePostulerAt(): ?\DateTimeImmutable
    {
        return $this->date_postuler_at;
    }

    public function setDatePostulerAt(\DateTimeImmutable $date_postuler_at): static
    {
        $this->date_postuler_at = $date_postuler_at;

        return $this;
    }
}
