<?php

namespace App\Entity;

use App\Repository\PublicityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicityRepository::class)]
class Publicity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $imagePub = null;

    #[ORM\Column(length: 255)]
    private ?string $lienOrientation = null;

    #[ORM\Column(length: 100)]
    private ?string $libele = null;

    #[ORM\Column(length: 100)]
    private ?string $demandeur = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagePub(): ?string
    {
        return $this->imagePub;
    }

    public function setImagePub(string $imagePub): static
    {
        $this->imagePub = $imagePub;

        return $this;
    }

    public function getLienOrientation(): ?string
    {
        return $this->lienOrientation;
    }

    public function setLienOrientation(string $lienOrientation): static
    {
        $this->lienOrientation = $lienOrientation;

        return $this;
    }

    public function getLibele(): ?string
    {
        return $this->libele;
    }

    public function setLibele(string $libele): static
    {
        $this->libele = $libele;

        return $this;
    }

    public function getDemandeur(): ?string
    {
        return $this->demandeur;
    }

    public function setDemandeur(string $demandeur): static
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }
}
