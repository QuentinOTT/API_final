<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250425184315 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auteur DROP FOREIGN KEY FK_55AB1403256915B');
        $this->addSql('CREATE TABLE adherent (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, code_commune VARCHAR(255) DEFAULT NULL, mail VARCHAR(255) NOT NULL, telephone VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nationalite (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pret (id INT AUTO_INCREMENT NOT NULL, livre_id INT NOT NULL, adherent_id INT NOT NULL, date_pret DATETIME NOT NULL, date_retour_prevue DATETIME NOT NULL, date_retour_reelle DATETIME DEFAULT NULL, INDEX IDX_52ECE97937D925CB (livre_id), INDEX IDX_52ECE97925F06C53 (adherent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pret ADD CONSTRAINT FK_52ECE97937D925CB FOREIGN KEY (livre_id) REFERENCES livre (id)');
        $this->addSql('ALTER TABLE pret ADD CONSTRAINT FK_52ECE97925F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent (id)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE nationnalite');
        $this->addSql('DROP INDEX IDX_55AB1403256915B ON auteur');
        $this->addSql('ALTER TABLE auteur ADD nationalite_id INT DEFAULT NULL, DROP relation_id');
        $this->addSql('ALTER TABLE auteur ADD CONSTRAINT FK_55AB1401B063272 FOREIGN KEY (nationalite_id) REFERENCES nationalite (id)');
        $this->addSql('CREATE INDEX IDX_55AB1401B063272 ON auteur (nationalite_id)');
        $this->addSql('ALTER TABLE livre CHANGE genre_id genre_id INT DEFAULT NULL, CHANGE annee annee INT NOT NULL, CHANGE langue langue VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auteur DROP FOREIGN KEY FK_55AB1401B063272');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0E3BD61CE (available_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE nationnalite (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pret DROP FOREIGN KEY FK_52ECE97937D925CB');
        $this->addSql('ALTER TABLE pret DROP FOREIGN KEY FK_52ECE97925F06C53');
        $this->addSql('DROP TABLE adherent');
        $this->addSql('DROP TABLE nationalite');
        $this->addSql('DROP TABLE pret');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP INDEX IDX_55AB1401B063272 ON auteur');
        $this->addSql('ALTER TABLE auteur ADD relation_id INT NOT NULL, DROP nationalite_id');
        $this->addSql('ALTER TABLE auteur ADD CONSTRAINT FK_55AB1403256915B FOREIGN KEY (relation_id) REFERENCES nationnalite (id)');
        $this->addSql('CREATE INDEX IDX_55AB1403256915B ON auteur (relation_id)');
        $this->addSql('ALTER TABLE livre CHANGE genre_id genre_id INT NOT NULL, CHANGE annee annee INT DEFAULT NULL, CHANGE langue langue VARCHAR(255) DEFAULT NULL');
    }
}
