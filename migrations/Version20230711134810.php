<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230711134810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496F83C31E');
        $this->addSql('DROP INDEX IDX_8D93D6496F83C31E ON user');
        $this->addSql('ALTER TABLE user DROP work_entry_id');
        $this->addSql('ALTER TABLE work_entry ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE work_entry ADD CONSTRAINT FK_3DAE65FA76ED395 FOREIGN KEY (user_id) REFERENCES work_entry (id)');
        $this->addSql('CREATE INDEX IDX_3DAE65FA76ED395 ON work_entry (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD work_entry_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496F83C31E FOREIGN KEY (work_entry_id) REFERENCES work_entry (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6496F83C31E ON user (work_entry_id)');
        $this->addSql('ALTER TABLE work_entry DROP FOREIGN KEY FK_3DAE65FA76ED395');
        $this->addSql('DROP INDEX IDX_3DAE65FA76ED395 ON work_entry');
        $this->addSql('ALTER TABLE work_entry DROP user_id');
    }
}
