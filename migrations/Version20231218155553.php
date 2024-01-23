<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218155553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE downtime_kpi DROP FOREIGN KEY downtime_kpi_ibfk_1');
        $this->addSql('DROP TABLE downtime');
        $this->addSql('DROP TABLE downtime_kpi');
        $this->addSql('ALTER TABLE candidate CHANGE id id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8B28E442BFE9F74 ON candidate (uuid_candidate)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE downtime (downtime_id INT AUTO_INCREMENT NOT NULL, downtime_service VARCHAR(75) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(downtime_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE downtime_kpi (id_downtime INT NOT NULL, downtime_kpi_date DATE NOT NULL, downtime_kpi_counter INT NOT NULL, downtime_kpi_type VARCHAR(75) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_downtime (id_downtime), UNIQUE INDEX downtime_kpi_date (downtime_kpi_date, downtime_kpi_type)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE downtime_kpi ADD CONSTRAINT downtime_kpi_ibfk_1 FOREIGN KEY (id_downtime) REFERENCES downtime (downtime_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_C8B28E442BFE9F74 ON candidate');
        $this->addSql('ALTER TABLE candidate CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
