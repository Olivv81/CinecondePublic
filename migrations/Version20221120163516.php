<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221120163516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493A1EDDEF');
        $this->addSql('DROP INDEX IDX_8D93D6493A1EDDEF ON user');
        $this->addSql('ALTER TABLE user DROP projection_benevole_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD projection_benevole_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493A1EDDEF FOREIGN KEY (projection_benevole_id) REFERENCES projection_benevole (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6493A1EDDEF ON user (projection_benevole_id)');
    }
}
