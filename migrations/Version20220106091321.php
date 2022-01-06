<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220106091321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checklist_call ADD checklist_id INT NOT NULL');
        $this->addSql('ALTER TABLE checklist_call ADD CONSTRAINT FK_4A7E2AD9B16D08A7 FOREIGN KEY (checklist_id) REFERENCES checklist (id)');
        $this->addSql('CREATE INDEX IDX_4A7E2AD9B16D08A7 ON checklist_call (checklist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `checklist_call` DROP FOREIGN KEY FK_4A7E2AD9B16D08A7');
        $this->addSql('DROP INDEX IDX_4A7E2AD9B16D08A7 ON `checklist_call`');
        $this->addSql('ALTER TABLE `checklist_call` DROP checklist_id');
    }
}
