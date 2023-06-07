<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230524192646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE horaire ADD inscription_accueil TINYINT(1) DEFAULT NULL, ADD inscription_projection TINYINT(1) DEFAULT NULL, CHANGE a_laffiche a_laffiche TINYINT(1) DEFAULT true');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE horaire DROP inscription_accueil, DROP inscription_projection, CHANGE a_laffiche a_laffiche TINYINT(1) DEFAULT 1');
    }
}
