<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210719125606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added new properties for bug_report table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report ADD application INT NOT NULL, ADD device_language VARCHAR(255) NOT NULL, ADD device_os VARCHAR(255) NOT NULL, ADD device_os_version VARCHAR(255) DEFAULT NULL, ADD browser VARCHAR(255) NOT NULL, ADD browser_version VARCHAR(255) DEFAULT NULL, ADD history LONGTEXT NOT NULL, ADD environment INT NOT NULL, ADD url VARCHAR(255) DEFAULT NULL, ADD account_id INT NOT NULL, ADD account_type INT NOT NULL, ADD item_id INT DEFAULT NULL, ADD other_info VARCHAR(255) DEFAULT NULL, CHANGE status device VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bug_report ADD status VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP application, DROP device, DROP device_language, DROP device_os, DROP device_os_version, DROP browser, DROP browser_version, DROP history, DROP environment, DROP url, DROP account_id, DROP account_type, DROP item_id, DROP other_info');
    }
}
