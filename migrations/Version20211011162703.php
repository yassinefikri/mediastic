<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211011162703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abstract_notification ADD triggerer_id INT NOT NULL');
        $this->addSql('ALTER TABLE abstract_notification ADD CONSTRAINT FK_2C3FCFFBA389BE23 FOREIGN KEY (triggerer_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2C3FCFFBA389BE23 ON abstract_notification (triggerer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abstract_notification DROP FOREIGN KEY FK_2C3FCFFBA389BE23');
        $this->addSql('DROP INDEX IDX_2C3FCFFBA389BE23 ON abstract_notification');
        $this->addSql('ALTER TABLE abstract_notification DROP triggerer_id');
    }
}
