<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711175222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD vorname VARCHAR(180) NOT NULL, ADD nachname VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64917039555 ON user (vorname)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D5C7CAD3 ON user (nachname)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D64917039555 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649D5C7CAD3 ON user');
        $this->addSql('ALTER TABLE user DROP vorname, DROP nachname');
    }
}
