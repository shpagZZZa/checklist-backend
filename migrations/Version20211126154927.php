<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126154927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `checklist_call` (id INT AUTO_INCREMENT NOT NULL, from_user_id INT NOT NULL, to_user_id INT NOT NULL, message VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, approval_type VARCHAR(255) NOT NULL, INDEX IDX_CC8E2F3E2130303A (from_user_id), INDEX IDX_CC8E2F3E29F6EE60 (to_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, base64 VARCHAR(255) NOT NULL, INDEX IDX_8C9F3610F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goal (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, title VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_FCDCEB2E8DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_527EDB25F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `checklist_call` ADD CONSTRAINT FK_CC8E2F3E2130303A FOREIGN KEY (from_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `checklist_call` ADD CONSTRAINT FK_CC8E2F3E29F6EE60 FOREIGN KEY (to_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2E8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');

        $this->addSql('CREATE TABLE `call_file_rel` (file_id INT NOT NULL, call_id INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX CALL_FILE_CALL_ID_INDEX (call_id), INDEX CALL_FILE_FILE_ID_INDEX (file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE call_file_rel ADD CONSTRAINT CALL_TASK_CALL_FOREIGN FOREIGN KEY (call_id) REFERENCES checklist_call (id)');
        $this->addSql('ALTER TABLE call_file_rel ADD CONSTRAINT CALL_TASK_FILE_FOREIGN FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE goal DROP FOREIGN KEY FK_FCDCEB2E8DB60186');
        $this->addSql('DROP TABLE `checklist_call`');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE goal');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE call_task_rel');
    }
}
