<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221024121052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_bug (tag_id INT NOT NULL, bug_id INT NOT NULL, PRIMARY KEY(tag_id, bug_id))');
        $this->addSql('CREATE INDEX IDX_34522516BAD26311 ON tag_bug (tag_id)');
        $this->addSql('CREATE INDEX IDX_34522516FA3DB3D5 ON tag_bug (bug_id)');
        $this->addSql('CREATE TABLE tag_feature (tag_id INT NOT NULL, feature_id INT NOT NULL, PRIMARY KEY(tag_id, feature_id))');
        $this->addSql('CREATE INDEX IDX_2DE658CCBAD26311 ON tag_feature (tag_id)');
        $this->addSql('CREATE INDEX IDX_2DE658CC60E4B879 ON tag_feature (feature_id)');
        $this->addSql('ALTER TABLE tag_bug ADD CONSTRAINT FK_34522516BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_bug ADD CONSTRAINT FK_34522516FA3DB3D5 FOREIGN KEY (bug_id) REFERENCES bug_report (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_feature ADD CONSTRAINT FK_2DE658CCBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_feature ADD CONSTRAINT FK_2DE658CC60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('ALTER TABLE tag_bug DROP CONSTRAINT FK_34522516BAD26311');
        $this->addSql('ALTER TABLE tag_bug DROP CONSTRAINT FK_34522516FA3DB3D5');
        $this->addSql('ALTER TABLE tag_feature DROP CONSTRAINT FK_2DE658CCBAD26311');
        $this->addSql('ALTER TABLE tag_feature DROP CONSTRAINT FK_2DE658CC60E4B879');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_bug');
        $this->addSql('DROP TABLE tag_feature');
    }
}
