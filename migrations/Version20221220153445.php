<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220153445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE feature DROP CONSTRAINT fk_1fd775665932f377');
        $this->addSql('DROP INDEX idx_1fd775665932f377');
        $this->addSql('ALTER TABLE feature ADD center VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feature DROP center_id');
        $this->addSql('DROP SEQUENCE center_id_seq');
        $this->addSql('DROP TABLE center');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE center_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE center (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE feature ADD center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feature DROP center');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT fk_1fd775665932f377 FOREIGN KEY (center_id) REFERENCES center (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1fd775665932f377 ON feature (center_id)');
    }
}
