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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachment DROP CONSTRAINT FK_795FD9BB60E4B879');
        $this->addSql('DROP INDEX IDX_795FD9BB60E4B879');
        $this->addSql('ALTER TABLE attachment DROP feature_id');
        $this->addSql('ALTER TABLE attachment ALTER bug_id SET NOT NULL');
    }
}
