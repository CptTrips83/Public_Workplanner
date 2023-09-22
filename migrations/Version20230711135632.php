<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711135632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_entry_kategorie ADD work_entry_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE work_entry_kategorie ADD CONSTRAINT FK_183F80DC6F83C31E FOREIGN KEY (work_entry_id) REFERENCES work_entry (id)');
        $this->addSql('CREATE INDEX IDX_183F80DC6F83C31E ON work_entry_kategorie (work_entry_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_entry_kategorie DROP FOREIGN KEY FK_183F80DC6F83C31E');
        $this->addSql('DROP INDEX IDX_183F80DC6F83C31E ON work_entry_kategorie');
        $this->addSql('ALTER TABLE work_entry_kategorie DROP work_entry_id');
    }
}
