<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220921181322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD phone_number INT NOT NULL, ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD discount SMALLINT NOT NULL, ADD address_first_line VARCHAR(255) NOT NULL, ADD address_second_line VARCHAR(255) DEFAULT NULL, ADD address_postal INT NOT NULL, ADD town VARCHAR(255) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP phone_number, DROP firstname, DROP lastname, DROP discount, DROP address_first_line, DROP address_second_line, DROP address_postal, DROP town, DROP created_at');
    }
}
