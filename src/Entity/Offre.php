<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $titre = null;

    #[ORM\Column(length: 100)]
    private ?string $secteur = null;

    #[ORM\Column(length: 50)]
    private ?string $type_contrat = null;

    #[ORM\Column(length: 50)]
    private ?string $lieu = null;

    #[ORM\Column(type: 'text')]
    private ?string $Description = null;

    #[ORM\ManyToOne(inversedBy: 'offres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entreprise $entreprise = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_mise_en_ligne_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_expiration_at = null;

    #[ORM\Column(type: Types::BINARY)]
    private $statut_offre = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::STRING, precision: 10, scale: 0, nullable: true)]
    private ?string $salaire = null;

    #[ORM\Column(length: 255)]
    private ?string $LienEmployeur = null;

    #[ORM\Column(length: 15)]
    private ?string $tempsTaff = null;

    #[ORM\Column(length: 100)]
    private ?string $illustrationImage = null;

    #[ORM\Column]
    private array $reponsabilities = [];

    #[ORM\Column]
    private array $competences = [];

    #[ORM\Column(length: 50)]
    private ?string $niveauRequis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AutreDetails = null;

    #[ORM\Column(length: 10)]
    private ?string $genre = null;

    #[ORM\Column(length: 10)]
    private ?string $experience = null;

    public function __construct()
    {

        $this->date_mise_en_ligne_at = new \DateTimeImmutable('now');
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getTypeContrat(): ?string
    {
        return $this->type_contrat;
    }

    public function setTypeContrat(string $type_contrat): static
    {
        $this->type_contrat = $type_contrat;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

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

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getDateMiseEnLigneAt(): ?\DateTimeImmutable
    {
        return $this->date_mise_en_ligne_at;
    }

    public function setDateMiseEnLigneAt(\DateTimeImmutable $date_mise_en_ligne_at): static
    {
        $this->date_mise_en_ligne_at = $date_mise_en_ligne_at;

        return $this;
    }

    public function getDateExpirationAt(): ?\DateTimeImmutable
    {
        return $this->date_expiration_at;
    }

    public function setDateExpirationAt(\DateTimeImmutable $date_expiration_at): static
    {
        $this->date_expiration_at = $date_expiration_at;

        return $this;
    }

    public function getStatutOffre()
    {
        return $this->statut_offre;
    }

    public function setStatutOffre($statut_offre): static
    {
        $this->statut_offre = $statut_offre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSalaire(): ?string
    {
        return $this->salaire;
    }

    public function setSalaire(?string $salaire): static
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getLienEmployeur(): ?string
    {
        return $this->LienEmployeur;
    }

    public function setLienEmployeur(string $LienEmployeur): static
    {
        $this->LienEmployeur = $LienEmployeur;

        return $this;
    }

    public function getTempsTaff(): ?string
    {
        return $this->tempsTaff;
    }

    public function setTempsTaff(string $tempsTaff): static
    {
        $this->tempsTaff = $tempsTaff;

        return $this;
    }

    public function getIllustrationImage(): ?string
    {
        return $this->illustrationImage;
    }

    public function setIllustrationImage(string $illustrationImage): static
    {
        $this->illustrationImage = $illustrationImage;

        return $this;
    }

    public function getReponsabilities(): array
    {
        // Vérifiez si la propriété 'reponsabilities' est vide
        $reponsabilities = $this->reponsabilities;
    
        // Si 'reponsabilities' est vide ou non défini, utilisez la valeur par défaut
        if (empty($reponsabilities)) {
            // Valeurs par défaut si la propriété est vide
            $reponsabilities = ["Responsabilité 1", "Responsabilité 2", "Responsabilité 3"];
        } else {
            // Si 'reponsabilities' est une chaîne JSON, décodez-la en tableau
            if (is_string($reponsabilities)) {
                $reponsabilities = json_decode($reponsabilities, true);
            }
        }
    
        return $reponsabilities;
    }

    public function setReponsabilities(array $reponsabilities): static
    {
        $this->reponsabilities = $reponsabilities;

        return $this;
    }

    public function getCompetences(): array
    {
        return $this->competences;
    }

    public function setCompetences(array $competences): static
    {
        $this->competences = $competences;

        return $this;
    }

    public function getNiveauRequis(): ?string
    {
        return $this->niveauRequis;
    }

    public function setNiveauRequis(string $niveauRequis): static
    {
        $this->niveauRequis = $niveauRequis;

        return $this;
    }

    public function getAutreDetails(): ?string
    {
        return $this->AutreDetails;
    }

    public function setAutreDetails(?string $AutreDetails): static
    {
        $this->AutreDetails = $AutreDetails;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): static
    {
        $this->experience = $experience;

        return $this;
    }
}
