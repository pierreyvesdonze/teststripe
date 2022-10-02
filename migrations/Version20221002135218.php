<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221002135218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_rate ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_rate ADD CONSTRAINT FK_A56D73F04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_A56D73F04584665A ON user_rate (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_rate DROP FOREIGN KEY FK_A56D73F04584665A');
        $this->addSql('DROP INDEX IDX_A56D73F04584665A ON user_rate');
        $this->addSql('ALTER TABLE user_rate DROP product_id');
    }
}
