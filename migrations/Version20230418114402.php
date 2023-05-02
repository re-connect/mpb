<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418114402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment ADD feature_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attachment ALTER bug_id DROP NOT NULL');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BB60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_795FD9BB60E4B879 ON attachment (feature_id)');
        $this->addSql('ALTER TABLE bug_report ALTER draft SET DEFAULT true');
        $this->addSql('ALTER TABLE bug_report ALTER draft SET NOT NULL');
        $this->addSql('ALTER TABLE feature ADD draft BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('UPDATE feature set draft=false');
        $this->addSql('ALTER TABLE feature ALTER application_id DROP NOT NULL');
        $this->addSql('ALTER TABLE feature ALTER title DROP NOT NULL');
        $this->addSql('ALTER TABLE feature ALTER content DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment DROP CONSTRAINT FK_795FD9BB60E4B879');
        $this->addSql('DROP INDEX IDX_795FD9BB60E4B879');
        $this->addSql('ALTER TABLE attachment DROP feature_id');
        $this->addSql('ALTER TABLE attachment ALTER bug_id SET NOT NULL');
        $this->addSql('ALTER TABLE bug_report ALTER draft DROP DEFAULT');
        $this->addSql('ALTER TABLE bug_report ALTER draft DROP NOT NULL');
        $this->addSql('ALTER TABLE feature DROP draft');
        $this->addSql('ALTER TABLE feature ALTER application_id SET NOT NULL');
        $this->addSql('ALTER TABLE feature ALTER title SET NOT NULL');
        $this->addSql('ALTER TABLE feature ALTER content SET NOT NULL');
    }
}
