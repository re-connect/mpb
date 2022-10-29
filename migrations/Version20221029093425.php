<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221029093425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE center_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE center (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE feature ADD center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD775665932F377 FOREIGN KEY (center_id) REFERENCES center (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1FD775665932F377 ON feature (center_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feature DROP CONSTRAINT FK_1FD775665932F377');
        $this->addSql('DROP SEQUENCE center_id_seq CASCADE');
        $this->addSql('DROP TABLE center');
        $this->addSql('DROP INDEX IDX_1FD775665932F377');
        $this->addSql('ALTER TABLE feature DROP center_id');
    }
}
