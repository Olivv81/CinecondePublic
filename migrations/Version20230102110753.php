<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230102110753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film CHANGE realisateurs realisateurs VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE horaire CHANGE a_laffiche a_laffiche TINYINT(1) DEFAULT true');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE film CHANGE realisateurs realisateurs VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE horaire CHANGE a_laffiche a_laffiche TINYINT(1) DEFAULT 1');
    }
}
