<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211205131543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD task_call_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB254F49B1C6 FOREIGN KEY (task_call_id) REFERENCES `checklist_call` (id)');
        $this->addSql('CREATE INDEX IDX_527EDB254F49B1C6 ON task (task_call_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB254F49B1C6');
        $this->addSql('DROP INDEX IDX_527EDB254F49B1C6 ON task');
        $this->addSql('ALTER TABLE task DROP task_call_id');
    }
}
