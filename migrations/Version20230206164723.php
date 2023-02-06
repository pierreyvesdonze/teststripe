<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230206164723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD discount_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id)');
        $this->addSql('CREATE INDEX IDX_F52993984C7C611F ON `order` (discount_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984C7C611F');
        $this->addSql('DROP INDEX IDX_F52993984C7C611F ON `order`');
        $this->addSql('ALTER TABLE `order` DROP discount_id');
    }
}
