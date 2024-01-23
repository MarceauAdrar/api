<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240107173237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE downtime_kpi ADD downtime_id INT NOT NULL');
        $this->addSql('ALTER TABLE downtime_kpi ADD CONSTRAINT FK_CA8DEEB3943157B FOREIGN KEY (downtime_id) REFERENCES downtime (id)');
        $this->addSql('CREATE INDEX IDX_CA8DEEB3943157B ON downtime_kpi (downtime_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE downtime_kpi DROP FOREIGN KEY FK_CA8DEEB3943157B');
        $this->addSql('DROP INDEX IDX_CA8DEEB3943157B ON downtime_kpi');
        $this->addSql('ALTER TABLE downtime_kpi DROP downtime_id');
    }
}
