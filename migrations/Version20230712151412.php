<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712151412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pause_kategorie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, bezahlt TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pause ADD kategorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE pause ADD CONSTRAINT FK_D79A92EDBAF991D3 FOREIGN KEY (kategorie_id) REFERENCES pause_kategorie (id)');
        $this->addSql('CREATE INDEX IDX_D79A92EDBAF991D3 ON pause (kategorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pause DROP FOREIGN KEY FK_D79A92EDBAF991D3');
        $this->addSql('DROP TABLE pause_kategorie');
        $this->addSql('DROP INDEX IDX_D79A92EDBAF991D3 ON pause');
        $this->addSql('ALTER TABLE pause DROP kategorie_id');
    }
}