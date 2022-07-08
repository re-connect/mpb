<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708115024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE attachment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE badge_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE bug_report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE preference_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE attachment (id INT NOT NULL, uploaded_by_id INT NOT NULL, bug_report_id INT NOT NULL, name VARCHAR(255) NOT NULL, size INT NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_795FD9BBA2B28FE8 ON attachment (uploaded_by_id)');
        $this->addSql('CREATE INDEX IDX_795FD9BB41193163 ON attachment (bug_report_id)');
        $this->addSql('CREATE TABLE badge (id INT NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE badge_user (badge_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(badge_id, user_id))');
        $this->addSql('CREATE INDEX IDX_299D3A50F7A2C2FC ON badge_user (badge_id)');
        $this->addSql('CREATE INDEX IDX_299D3A50A76ED395 ON badge_user (user_id)');
        $this->addSql('CREATE TABLE bug_report (id INT NOT NULL, user_id INT NOT NULL, category_id INT DEFAULT NULL, status_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, application INT NOT NULL, device VARCHAR(255) NOT NULL, device_language VARCHAR(255) NOT NULL, device_os_version VARCHAR(255) DEFAULT NULL, browser VARCHAR(255) NOT NULL, browser_version VARCHAR(255) DEFAULT NULL, history TEXT DEFAULT NULL, environment INT NOT NULL, url VARCHAR(255) DEFAULT NULL, account_id INT DEFAULT NULL, account_type INT NOT NULL, item_id INT DEFAULT NULL, other_info VARCHAR(255) DEFAULT NULL, user_in_charge VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F6F2DC7AA76ED395 ON bug_report (user_id)');
        $this->addSql('CREATE INDEX IDX_F6F2DC7A12469DE2 ON bug_report (category_id)');
        $this->addSql('CREATE INDEX IDX_F6F2DC7A6BF700BD ON bug_report (status_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, user_id INT NOT NULL, bug_report_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE INDEX IDX_9474526C41193163 ON comment (bug_report_id)');
        $this->addSql('CREATE TABLE preference (id INT NOT NULL, user_id INT NOT NULL, has_accepted_slack BOOLEAN NOT NULL, has_accepted_email BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D69B053A76ED395 ON preference (user_id)');
        $this->addSql('CREATE TABLE status (id INT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BBA2B28FE8 FOREIGN KEY (uploaded_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BB41193163 FOREIGN KEY (bug_report_id) REFERENCES bug_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE badge_user ADD CONSTRAINT FK_299D3A50F7A2C2FC FOREIGN KEY (badge_id) REFERENCES badge (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE badge_user ADD CONSTRAINT FK_299D3A50A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bug_report ADD CONSTRAINT FK_F6F2DC7A6BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C41193163 FOREIGN KEY (bug_report_id) REFERENCES bug_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE preference ADD CONSTRAINT FK_5D69B053A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE badge_user DROP CONSTRAINT FK_299D3A50F7A2C2FC');
        $this->addSql('ALTER TABLE attachment DROP CONSTRAINT FK_795FD9BB41193163');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C41193163');
        $this->addSql('ALTER TABLE bug_report DROP CONSTRAINT FK_F6F2DC7A12469DE2');
        $this->addSql('ALTER TABLE bug_report DROP CONSTRAINT FK_F6F2DC7A6BF700BD');
        $this->addSql('ALTER TABLE attachment DROP CONSTRAINT FK_795FD9BBA2B28FE8');
        $this->addSql('ALTER TABLE badge_user DROP CONSTRAINT FK_299D3A50A76ED395');
        $this->addSql('ALTER TABLE bug_report DROP CONSTRAINT FK_F6F2DC7AA76ED395');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE preference DROP CONSTRAINT FK_5D69B053A76ED395');
        $this->addSql('DROP SEQUENCE attachment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE badge_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE bug_report_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE preference_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE status_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP TABLE badge');
        $this->addSql('DROP TABLE badge_user');
        $this->addSql('DROP TABLE bug_report');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE preference');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE users');
    }
}
