<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240315140058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tag ADD icon VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tag_feature DROP CONSTRAINT FK_2DE658CC60E4B879');
        $this->addSql('ALTER TABLE tag_feature DROP CONSTRAINT FK_2DE658CCBAD26311');
        $this->addSql('ALTER TABLE tag_feature DROP CONSTRAINT tag_feature_pkey');
        $this->addSql('ALTER TABLE tag_feature ADD CONSTRAINT FK_2DE658CC60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_feature ADD CONSTRAINT FK_2DE658CCBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_feature ADD PRIMARY KEY (feature_id, tag_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tag DROP icon');
        $this->addSql('ALTER TABLE tag_feature DROP CONSTRAINT fk_2de658cc60e4b879');
        $this->addSql('ALTER TABLE tag_feature DROP CONSTRAINT fk_2de658ccbad26311');
        $this->addSql('DROP INDEX tag_feature_pkey');
        $this->addSql('ALTER TABLE tag_feature ADD CONSTRAINT fk_2de658cc60e4b879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_feature ADD CONSTRAINT fk_2de658ccbad26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_feature ADD PRIMARY KEY (tag_id, feature_id)');
    }
}
