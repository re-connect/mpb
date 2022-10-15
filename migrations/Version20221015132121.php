<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221015132121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE vote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE vote (id INT NOT NULL, bug_id INT DEFAULT NULL, feature_id INT DEFAULT NULL, voter_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A108564FA3DB3D5 ON vote (bug_id)');
        $this->addSql('CREATE INDEX IDX_5A10856460E4B879 ON vote (feature_id)');
        $this->addSql('CREATE INDEX IDX_5A108564EBB4B8AD ON vote (voter_id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564FA3DB3D5 FOREIGN KEY (bug_id) REFERENCES bug_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856460E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564EBB4B8AD FOREIGN KEY (voter_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE vote_id_seq CASCADE');
        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A108564FA3DB3D5');
        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A10856460E4B879');
        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A108564EBB4B8AD');
        $this->addSql('DROP TABLE vote');
    }
}
