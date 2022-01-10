<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220110154249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE call_file_rel (id INT AUTO_INCREMENT NOT NULL, call_id INT NOT NULL, file_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_8D9F389E50A89B2C (call_id), INDEX IDX_8D9F389E93CB796C (file_id), INDEX IDX_8D9F389EF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_file_rel ADD CONSTRAINT FK_8D9F389E50A89B2C FOREIGN KEY (call_id) REFERENCES `checklist_call` (id)');
        $this->addSql('ALTER TABLE call_file_rel ADD CONSTRAINT FK_8D9F389E93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE call_file_rel ADD CONSTRAINT FK_8D9F389EF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE call_file_rel');
    }
}
