<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715102331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE application_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE application (id INT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE bug_report ADD application_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report DROP application');
        $this->addSql('ALTER TABLE bug_report DROP device');
        $this->addSql('ALTER TABLE bug_report DROP device_language');
        $this->addSql('ALTER TABLE bug_report DROP device_os_version');
        $this->addSql('ALTER TABLE bug_report DROP browser');
        $this->addSql('ALTER TABLE bug_report DROP browser_version');
        $this->addSql('ALTER TABLE bug_report DROP history');
        $this->addSql('ALTER TABLE bug_report DROP environment');
        $this->addSql('ALTER TABLE bug_report DROP other_info');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7A3E030ACD FOREIGN KEY (application_id) REFERENCES application (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F6F2DC7A3E030ACD ON bug_report (application_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report DROP CONSTRAINT FK_F6F2DC7A3E030ACD');
        $this->addSql('DROP SEQUENCE application_id_seq CASCADE');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP INDEX IDX_F6F2DC7A3E030ACD');
        $this->addSql('ALTER TABLE bug_report ADD application INT NOT NULL');
        $this->addSql('ALTER TABLE bug_report ADD device VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bug_report ADD device_language VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bug_report ADD device_os_version VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report ADD browser VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bug_report ADD browser_version VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report ADD history TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report ADD environment INT NOT NULL');
        $this->addSql('ALTER TABLE bug_report ADD other_info VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report DROP application_id');
    }
}
