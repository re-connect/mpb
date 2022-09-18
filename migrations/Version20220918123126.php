<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918123126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE feature_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE feature (id INT NOT NULL, application_id INT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, done BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1FD775663E030ACD ON feature (application_id)');
        $this->addSql('CREATE INDEX IDX_1FD77566A76ED395 ON feature (user_id)');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD775663E030ACD FOREIGN KEY (application_id) REFERENCES application (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feature ADD CONSTRAINT FK_1FD77566A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bug_report ALTER draft DROP DEFAULT');
        $this->addSql('ALTER TABLE comment ADD feature_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ALTER bug_id DROP NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526C60E4B879 ON comment (feature_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C60E4B879');
        $this->addSql('DROP SEQUENCE feature_id_seq CASCADE');
        $this->addSql('ALTER TABLE feature DROP CONSTRAINT FK_1FD775663E030ACD');
        $this->addSql('ALTER TABLE feature DROP CONSTRAINT FK_1FD77566A76ED395');
        $this->addSql('DROP TABLE feature');
        $this->addSql('ALTER TABLE bug_report ALTER draft SET DEFAULT false');
        $this->addSql('DROP INDEX IDX_9474526C60E4B879');
        $this->addSql('ALTER TABLE comment DROP feature_id');
        $this->addSql('ALTER TABLE comment ALTER bug_id SET NOT NULL');
    }
}
