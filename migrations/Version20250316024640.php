<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316024640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings CHANGE titre titre LONGTEXT NOT NULL, CHANGE sous_titre sous_titre LONGTEXT NOT NULL, CHANGE titre_bande titre_bande LONGTEXT NOT NULL, CHANGE sous_titre_bande sous_titre_bande LONGTEXT NOT NULL, CHANGE titre_stat titre_stat LONGTEXT NOT NULL, CHANGE sous_titre_stat sous_titre_stat LONGTEXT NOT NULL, CHANGE email email VARCHAR(100) NOT NULL, CHANGE secteur_activite secteur_activite VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE settings CHANGE titre titre VARCHAR(100) NOT NULL, CHANGE sous_titre sous_titre VARCHAR(100) NOT NULL, CHANGE titre_bande titre_bande VARCHAR(100) NOT NULL, CHANGE sous_titre_bande sous_titre_bande VARCHAR(100) NOT NULL, CHANGE titre_stat titre_stat VARCHAR(100) NOT NULL, CHANGE sous_titre_stat sous_titre_stat VARCHAR(100) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE secteur_activite secteur_activite VARCHAR(255) NOT NULL');
    }
}
