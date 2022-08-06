<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806132304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment DROP CONSTRAINT fk_795fd9bb41193163');
        $this->addSql('DROP INDEX idx_795fd9bb41193163');
        $this->addSql('ALTER TABLE attachment RENAME COLUMN bug_report_id TO bug_id');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BBFA3DB3D5 FOREIGN KEY (bug_id) REFERENCES bug_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_795FD9BBFA3DB3D5 ON attachment (bug_id)');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT fk_9474526c41193163');
        $this->addSql('DROP INDEX idx_9474526c41193163');
        $this->addSql('ALTER TABLE comment RENAME COLUMN bug_report_id TO bug_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CFA3DB3D5 FOREIGN KEY (bug_id) REFERENCES bug_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526CFA3DB3D5 ON comment (bug_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment DROP CONSTRAINT FK_795FD9BBFA3DB3D5');
        $this->addSql('DROP INDEX IDX_795FD9BBFA3DB3D5');
        $this->addSql('ALTER TABLE attachment RENAME COLUMN bug_id TO bug_report_id');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT fk_795fd9bb41193163 FOREIGN KEY (bug_report_id) REFERENCES bug_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_795fd9bb41193163 ON attachment (bug_report_id)');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CFA3DB3D5');
        $this->addSql('DROP INDEX IDX_9474526CFA3DB3D5');
        $this->addSql('ALTER TABLE comment RENAME COLUMN bug_id TO bug_report_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT fk_9474526c41193163 FOREIGN KEY (bug_report_id) REFERENCES bug_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9474526c41193163 ON comment (bug_report_id)');
    }
}
