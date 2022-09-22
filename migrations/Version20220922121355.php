<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220922121355 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product ADD category_product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD639A3624 FOREIGN KEY (category_product_id) REFERENCES category_product (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD639A3624 ON product (category_product_id)');
        $this->addSql('ALTER TABLE user CHANGE phone_number phone_number INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD639A3624');
        $this->addSql('DROP TABLE category_product');
        $this->addSql('DROP INDEX IDX_D34A04AD639A3624 ON product');
        $this->addSql('ALTER TABLE product DROP category_product_id');
        $this->addSql('ALTER TABLE user CHANGE phone_number phone_number INT DEFAULT NULL');
    }
}
