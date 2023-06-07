<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220919184753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_nl (id INT AUTO_INCREMENT NOT NULL, e_mail VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE news_letter_contact_nl (news_letter_id INT NOT NULL, contact_nl_id INT NOT NULL, INDEX IDX_F753211E5900A0E7 (news_letter_id), INDEX IDX_F753211E25FA7AA7 (contact_nl_id), PRIMARY KEY(news_letter_id, contact_nl_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE news_letter_contact_nl ADD CONSTRAINT FK_F753211E5900A0E7 FOREIGN KEY (news_letter_id) REFERENCES news_letter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE news_letter_contact_nl ADD CONSTRAINT FK_F753211E25FA7AA7 FOREIGN KEY (contact_nl_id) REFERENCES contact_nl (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE news_letter_contact_nl DROP FOREIGN KEY FK_F753211E25FA7AA7');
        $this->addSql('DROP TABLE contact_nl');
        $this->addSql('DROP TABLE news_letter_contact_nl');
    }
}
