<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240107172729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE downtime (id INT AUTO_INCREMENT NOT NULL, service VARCHAR(75) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE downtime_kpi (id INT AUTO_INCREMENT NOT NULL, kpi_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', kpi_counter INT NOT NULL, kpi_type VARCHAR(75) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate CHANGE is_enabled_candidate is_enabled_candidate TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE downtime');
        $this->addSql('DROP TABLE downtime_kpi');
        $this->addSql('ALTER TABLE candidate CHANGE is_enabled_candidate is_enabled_candidate TINYINT(1) DEFAULT 0 NOT NULL');
    }
}
