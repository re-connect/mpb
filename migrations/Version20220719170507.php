<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719170507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE user_kind_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE bug_report ALTER COLUMN account_type DROP NOT NULL');
        $this->addSql('UPDATE bug_report SET account_type=NULL');
        $this->addSql('CREATE TABLE user_kind (id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE application ALTER icon DROP NOT NULL');
        $this->addSql('ALTER TABLE application ALTER color DROP NOT NULL');
        $this->addSql('ALTER TABLE bug_report ADD attachement_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report RENAME COLUMN account_type TO user_kind_id');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7A686D32A3 FOREIGN KEY (user_kind_id) REFERENCES user_kind (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F6F2DC7A686D32A3 ON bug_report (user_kind_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report DROP CONSTRAINT FK_F6F2DC7A686D32A3');
        $this->addSql('DROP SEQUENCE user_kind_id_seq CASCADE');
        $this->addSql('DROP TABLE user_kind');
        $this->addSql('DROP INDEX IDX_F6F2DC7A686D32A3');
        $this->addSql('ALTER TABLE bug_report DROP attachement_name');
        $this->addSql('ALTER TABLE bug_report RENAME COLUMN user_kind_id TO account_type');
        $this->addSql('ALTER TABLE application ALTER color SET NOT NULL');
        $this->addSql('ALTER TABLE application ALTER icon SET NOT NULL');
    }
}
