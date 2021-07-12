<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519195434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE film (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, realisateurs VARCHAR(255) NOT NULL, acteurs VARCHAR(255) DEFAULT NULL, anneeproduction DATETIME DEFAULT NULL, date_sortie DATETIME DEFAULT NULL, duree TIME DEFAULT NULL, genre_principal VARCHAR(255) DEFAULT NULL, nationalite VARCHAR(255) DEFAULT NULL, synopsis VARCHAR(2000) DEFAULT NULL, affichette VARCHAR(500) DEFAULT NULL, video VARCHAR(255) NOT NULL, visa_number INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE horaire (id INT AUTO_INCREMENT NOT NULL, id_film_id INT NOT NULL, vo TINYINT(1) NOT NULL, version VARCHAR(255) DEFAULT NULL, projection VARCHAR(255) DEFAULT NULL, soustitre VARCHAR(255) DEFAULT NULL, seances LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_BBC83DB688E2F8F3 (id_film_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE horaire ADD CONSTRAINT FK_BBC83DB688E2F8F3 FOREIGN KEY (id_film_id) REFERENCES film (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE horaire DROP FOREIGN KEY FK_BBC83DB688E2F8F3');
        $this->addSql('DROP TABLE film');
        $this->addSql('DROP TABLE horaire');
    }
}
