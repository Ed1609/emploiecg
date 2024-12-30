<?php

namespace App\Entity;

use App\Repository\AbonneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: AbonneRepository::class)]
#[UniqueEntity(fields: "msisdn", message: "Ce numéro MSISDN est déjà enregistré.")]
class Abonne implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 15)]
    private ?string $msisdn = null;

    #[ORM\Column(length: 50)]
    private ?string $Ville = null;

    #[ORM\Column(length: 100)]
    private ?string $specialite = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?int $tentativeconnexion = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isLocked = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $lockedUntil = null;

    public function __construct()
    {
        $this->roles = ['ROLE_USER'];
        $this->tentativeconnexion = 0;
        $this->isLocked = false;
    }
    public function getUserIdentifier(): string
    {
        return $this->msisdn;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMsisdn(): ?string
    {
        return $this->msisdn;
    }

    public function setMsisdn(string $msisdn): static
    {
        $this->msisdn = $msisdn;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): static
    {
        $this->Ville = $Ville;
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

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;
        return $this;
    }

    public function getRoles(): array
    {
        // Garantie que chaque utilisateur a au moins le rôle "ROLE_USER"
        return array_unique(array_merge($this->roles, ['ROLE_USER']));
    }
    
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }
    

    public function getTentativeconnexion(): ?int
    {
        return $this->tentativeconnexion;
    }

    public function setTentativeconnexion(int $tentativeconnexion): static
    {
        $this->tentativeconnexion = $tentativeconnexion;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Efface les données sensibles temporaires si nécessaire
    }

    public function isLocked(): bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): static
    {
        $this->isLocked = $isLocked;
        return $this;
    }

    public function getLockedUntil(): ?\DateTime
    {
        return $this->lockedUntil;
    }

    public function setLockedUntil(?\DateTime $lockedUntil): static
    {
        $this->lockedUntil = $lockedUntil;
        return $this;
    }
}
