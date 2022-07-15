<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715125124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report DROP CONSTRAINT fk_f6f2dc7a12469de2');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP INDEX idx_f6f2dc7a12469de2');
        $this->addSql('ALTER TABLE bug_report DROP user_in_charge');
        $this->addSql('ALTER TABLE bug_report ALTER done DROP DEFAULT');
        $this->addSql('ALTER TABLE bug_report RENAME COLUMN category_id TO assignee_id');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7A59EC7D60 FOREIGN KEY (assignee_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F6F2DC7A59EC7D60 ON bug_report (assignee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE bug_report DROP CONSTRAINT FK_F6F2DC7A59EC7D60');
        $this->addSql('DROP INDEX IDX_F6F2DC7A59EC7D60');
        $this->addSql('ALTER TABLE bug_report ADD user_in_charge VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE bug_report ALTER done SET DEFAULT false');
        $this->addSql('ALTER TABLE bug_report RENAME COLUMN assignee_id TO category_id');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT fk_f6f2dc7a12469de2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f6f2dc7a12469de2 ON bug_report (category_id)');
    }
}
