<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221025164559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accueil_benevole (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projection_benevole (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenement ADD selection VARBINARY(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE film ADD selection VARBINARY(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD accueil_benevole_id INT DEFAULT NULL, ADD projection_benevole_id INT DEFAULT NULL, ADD selection VARBINARY(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EB034480 FOREIGN KEY (accueil_benevole_id) REFERENCES accueil_benevole (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493A1EDDEF FOREIGN KEY (projection_benevole_id) REFERENCES projection_benevole (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649EB034480 ON user (accueil_benevole_id)');
        $this->addSql('CREATE INDEX IDX_8D93D6493A1EDDEF ON user (projection_benevole_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EB034480');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493A1EDDEF');
        $this->addSql('DROP TABLE accueil_benevole');
        $this->addSql('DROP TABLE projection_benevole');
        $this->addSql('ALTER TABLE evenement DROP selection');
        $this->addSql('ALTER TABLE film DROP selection');
        $this->addSql('DROP INDEX IDX_8D93D649EB034480 ON user');
        $this->addSql('DROP INDEX IDX_8D93D6493A1EDDEF ON user');
        $this->addSql('ALTER TABLE user DROP accueil_benevole_id, DROP projection_benevole_id, DROP selection');
    }
}
