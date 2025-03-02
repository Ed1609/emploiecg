<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302212745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, identifiant VARCHAR(100) NOT NULL, nom_plateforme VARCHAR(100) NOT NULL, utilisateur_plateforme VARCHAR(100) NOT NULL, logo_entete VARCHAR(255) DEFAULT NULL, logo_nav_bar VARCHAR(255) DEFAULT NULL, mot_cle VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, image_accueil VARCHAR(255) NOT NULL, titre VARCHAR(100) NOT NULL, sous_titre VARCHAR(100) NOT NULL, titre_bande VARCHAR(100) NOT NULL, sous_titre_bande VARCHAR(100) NOT NULL, titre_stat VARCHAR(100) NOT NULL, sous_titre_stat VARCHAR(100) NOT NULL, abonnements INT NOT NULL, offres_postulees INT NOT NULL, emplois_pourvus INT NOT NULL, entreprise INT NOT NULL, lien_facebook VARCHAR(255) NOT NULL, lien_twitter VARCHAR(255) NOT NULL, lien_instagram VARCHAR(255) NOT NULL, linked_in VARCHAR(255) NOT NULL, addresse VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, secteur_activite VARCHAR(255) NOT NULL, situation_geographique VARCHAR(255) NOT NULL, text_footer LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE settings');
    }
}
