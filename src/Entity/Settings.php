<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    // Paramètres de base
    #[ORM\Column(length: 100)]
    private ?string $identifiant = null;

    #[ORM\Column(length: 100)]
    private ?string $nomPlateforme = null;

    #[ORM\Column(length: 100)]
    private ?string $utilisateurPlateforme = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoEntete = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoNavBar = null;

    // Header
    #[ORM\Column(length: 100)]
    private ?string $motCle = null;

    #[ORM\Column(type: "text")]
    private ?string $description = null;

    // Page d'accueil
    #[ORM\Column(length: 255)]
    private ?string $imageAccueil = null;

    #[ORM\Column(type: "text")]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    private ?string $sousTitre = null;

    // Bande d'action
    #[ORM\Column(type: "text")]
    private ?string $titreBande = null;

    #[ORM\Column(type: "text")]
    private ?string $sousTitreBande = null;

    // Statistique
    #[ORM\Column(type: "text")]
    private ?string $titreStat = null;

    #[ORM\Column(type: "text")]
    private ?string $sousTitreStat = null;

    #[ORM\Column(type: "integer")]
    private ?int $abonnements = null;

    #[ORM\Column(type: "integer")]
    private ?int $offresPostulees = null;

    #[ORM\Column(type: "integer")]
    private ?int $emploisPourvus = null;

    #[ORM\Column(type: "integer")]
    private ?int $entreprise = null;

    // Liens
    #[ORM\Column(length: 255)]
    private ?string $lienFacebook = null;

    #[ORM\Column(length: 255)]
    private ?string $lienTwitter = null;

    #[ORM\Column(length: 255)]
    private ?string $lienInstagram = null;

    #[ORM\Column(length: 255)]
    private ?string $linkedIn = null;

    // Contact et Infos
    #[ORM\Column(length: 255)]
    private ?string $addresse = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    private ?string $secteurActivite = null;

    #[ORM\Column(length: 255)]
    private ?string $situationGeographique = null;

    #[ORM\Column(type: "text")]
    private ?string $textFooter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePropos = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagePropos2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageDocuments = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;
        return $this;
    }

    public function getNomPlateforme(): ?string
    {
        return $this->nomPlateforme;
    }

    public function setNomPlateforme(string $nomPlateforme): self
    {
        $this->nomPlateforme = $nomPlateforme;
        return $this;
    }

    public function getUtilisateurPlateforme(): ?string
    {
        return $this->utilisateurPlateforme;
    }

    public function setUtilisateurPlateforme(string $utilisateurPlateforme): self
    {
        $this->utilisateurPlateforme = $utilisateurPlateforme;
        return $this;
    }

    public function getLogoEntete(): ?string
    {
        return $this->logoEntete;
    }

    public function setLogoEntete(?string $logoEntete): self
    {
        $this->logoEntete = $logoEntete;
        return $this;
    }

    public function getLogoNavBar(): ?string
    {
        return $this->logoNavBar;
    }

    public function setLogoNavBar(?string $logoNavBar): self
    {
        $this->logoNavBar = $logoNavBar;
        return $this;
    }

    public function getMotCle(): ?string
    {
        return $this->motCle;
    }

    public function setMotCle(string $motCle): self
    {
        $this->motCle = $motCle;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImageAccueil(): ?string
    {
        return $this->imageAccueil;
    }

    public function setImageAccueil(string $imageAccueil): self
    {
        $this->imageAccueil = $imageAccueil;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getSousTitre(): ?string
    {
        return $this->sousTitre;
    }

    public function setSousTitre(string $sousTitre): self
    {
        $this->sousTitre = $sousTitre;
        return $this;
    }

    public function getTitreBande(): ?string
    {
        return $this->titreBande;
    }

    public function setTitreBande(string $titreBande): self
    {
        $this->titreBande = $titreBande;
        return $this;
    }

    public function getSousTitreBande(): ?string
    {
        return $this->sousTitreBande;
    }

    public function setSousTitreBande(string $sousTitreBande): self
    {
        $this->sousTitreBande = $sousTitreBande;
        return $this;
    }

    public function getTitreStat(): ?string
    {
        return $this->titreStat;
    }

    public function setTitreStat(string $titreStat): self
    {
        $this->titreStat = $titreStat;
        return $this;
    }

    public function getSousTitreStat(): ?string
    {
        return $this->sousTitreStat;
    }

    public function setSousTitreStat(string $sousTitreStat): self
    {
        $this->sousTitreStat = $sousTitreStat;
        return $this;
    }

    public function getAbonnements(): ?int
    {
        return $this->abonnements;
    }

    public function setAbonnements(int $abonnements): self
    {
        $this->abonnements = $abonnements;
        return $this;
    }

    public function getOffresPostulees(): ?int
    {
        return $this->offresPostulees;
    }

    public function setOffresPostulees(int $offresPostulees): self
    {
        $this->offresPostulees = $offresPostulees;
        return $this;
    }

    public function getEmploisPourvus(): ?int
    {
        return $this->emploisPourvus;
    }

    public function setEmploisPourvus(int $emploisPourvus): self
    {
        $this->emploisPourvus = $emploisPourvus;
        return $this;
    }

    public function getEntreprise(): ?int
    {
        return $this->entreprise;
    }

    public function setEntreprise(int $entreprise): self
    {
        $this->entreprise = $entreprise;
        return $this;
    }

    public function getLienFacebook(): ?string
    {
        return $this->lienFacebook;
    }

    public function setLienFacebook(string $lienFacebook): self
    {
        $this->lienFacebook = $lienFacebook;
        return $this;
    }

    public function getLienTwitter(): ?string
    {
        return $this->lienTwitter;
    }

    public function setLienTwitter(string $lienTwitter): self
    {
        $this->lienTwitter = $lienTwitter;
        return $this;
    }

    public function getLienInstagram(): ?string
    {
        return $this->lienInstagram;
    }

    public function setLienInstagram(string $lienInstagram): self
    {
        $this->lienInstagram = $lienInstagram;
        return $this;
    }

    public function getLinkedIn(): ?string
    {
        return $this->linkedIn;
    }

    public function setLinkedIn(string $linkedIn): self
    {
        $this->linkedIn = $linkedIn;
        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): self
    {
        $this->addresse = $addresse;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getSecteurActivite(): ?string
    {
        return $this->secteurActivite;
    }

    public function setSecteurActivite(string $secteurActivite): self
    {
        $this->secteurActivite = $secteurActivite;
        return $this;
    }

    public function getSituationGeographique(): ?string
    {
        return $this->situationGeographique;
    }

    public function setSituationGeographique(string $situationGeographique): self
    {
        $this->situationGeographique = $situationGeographique;
        return $this;
    }

    public function getTextFooter(): ?string
    {
        return $this->textFooter;
    }

    public function setTextFooter(string $textFooter): self
    {
        $this->textFooter = $textFooter;
        return $this;
    }

    public function getImagePropos(): ?string
    {
        return $this->imagePropos;
    }

    public function setImagePropos(?string $imagePropos): static
    {
        $this->imagePropos = $imagePropos;

        return $this;
    }

    public function getImagePropos2(): ?string
    {
        return $this->imagePropos2;
    }

    public function setImagePropos2(?string $imagePropos2): static
    {
        $this->imagePropos2 = $imagePropos2;

        return $this;
    }

    public function getImageContact(): ?string
    {
        return $this->imageContact;
    }

    public function setImageContact(?string $imageContact): static
    {
        $this->imageContact = $imageContact;

        return $this;
    }

    public function getImageDocuments(): ?string
    {
        return $this->imageDocuments;
    }

    public function setImageDocuments(?string $imageDocuments): static
    {
        $this->imageDocuments = $imageDocuments;

        return $this;
    }
}
