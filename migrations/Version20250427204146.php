<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427204146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pret ADD dateRetourReelle DATETIME DEFAULT NULL, ADD retourne TINYINT(1) NOT NULL, DROP date_retour_prevue, CHANGE date_retour_reelle dateRetourPrevue DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pret ADD date_retour_prevue DATETIME NOT NULL, ADD date_retour_reelle DATETIME DEFAULT NULL, DROP dateRetourPrevue, DROP dateRetourReelle, DROP retourne');
    }
}
