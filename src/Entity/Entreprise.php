<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libele = null;

    #[ORM\Column(length: 100)]
    private ?string $secteur = null;

    #[ORM\Column(type: 'text')]
    private ?string $Description = null;

    #[ORM\Column(length: 255)]
    private ?string $situation_geographique = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Offre::class, cascade: ['persist', 'remove'])]
    private Collection $offres;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date_mise_en_ligne_at = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    public function __construct()
    {
        $this->offres = new ArrayCollection();
        $this->date_mise_en_ligne_at = new \DateTimeImmutable('now');
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(string $secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getSituationGeographique(): ?string
    {
        return $this->situation_geographique;
    }

    public function setSituationGeographique(string $situation_geographique): static
    {
        $this->situation_geographique = $situation_geographique;

        return $this;
    }

    /**
     * @return Collection<int, Offre>
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function getDateMiseEnLigneAt(): ?\DateTimeInterface
    {
        return $this->date_mise_en_ligne_at;
    }

    public function setDateMiseEnLigneAt(\DateTimeInterface $date_mise_en_ligne_at): static
    {
        $this->date_mise_en_ligne_at = $date_mise_en_ligne_at;
        return $this;
    }
    public function addOffre(Offre $offre): static
    {
        if (!$this->offres->contains($offre)) {
            $this->offres->add($offre);
            $offre->setEntreprise($this);
        }

        return $this;
    }

    public function removeOffre(Offre $offre): static
    {
        if ($this->offres->removeElement($offre)) {

            if ($offre->getEntreprise() === $this) {
                $offre->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }
}
