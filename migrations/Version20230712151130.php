<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712151130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pause (id INT AUTO_INCREMENT NOT NULL, workentry_id INT NOT NULL, start_datum DATETIME NOT NULL, ende_datum DATETIME DEFAULT NULL, INDEX IDX_D79A92ED1BB5101D (workentry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pause ADD CONSTRAINT FK_D79A92ED1BB5101D FOREIGN KEY (workentry_id) REFERENCES work_entry (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pause DROP FOREIGN KEY FK_D79A92ED1BB5101D');
        $this->addSql('DROP TABLE pause');
    }
}
