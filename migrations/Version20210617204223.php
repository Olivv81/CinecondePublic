<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210617204223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement_film (evenement_id INT NOT NULL, film_id INT NOT NULL, INDEX IDX_2117DBAFFD02F13 (evenement_id), INDEX IDX_2117DBAF567F5183 (film_id), PRIMARY KEY(evenement_id, film_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenement_film ADD CONSTRAINT FK_2117DBAFFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_film ADD CONSTRAINT FK_2117DBAF567F5183 FOREIGN KEY (film_id) REFERENCES film (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE film DROP FOREIGN KEY FK_8244BE22FD02F13');
        $this->addSql('DROP INDEX IDX_8244BE22FD02F13 ON film');
        $this->addSql('ALTER TABLE film DROP evenement_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE evenement_film');
        $this->addSql('ALTER TABLE film ADD evenement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE film ADD CONSTRAINT FK_8244BE22FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('CREATE INDEX IDX_8244BE22FD02F13 ON film (evenement_id)');
    }
}
