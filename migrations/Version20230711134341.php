<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711134341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE work_entry (id INT AUTO_INCREMENT NOT NULL, start_datum DATETIME NOT NULL, ende_datum DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD work_entry_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496F83C31E FOREIGN KEY (work_entry_id) REFERENCES work_entry (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496F83C31E ON user (work_entry_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496F83C31E');
        $this->addSql('DROP TABLE work_entry');
        $this->addSql('DROP INDEX IDX_8D93D6496F83C31E ON user');
        $this->addSql('ALTER TABLE user DROP work_entry_id');
    }
}
