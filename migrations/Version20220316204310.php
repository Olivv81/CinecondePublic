<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316204310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD video_vimeo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE film ADD video_vimeo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE horaire CHANGE seance_id seance_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement DROP video_vimeo');
        $this->addSql('ALTER TABLE film DROP video_vimeo');
        $this->addSql('ALTER TABLE horaire CHANGE seance_id seance_id INT DEFAULT NULL');
    }
}
