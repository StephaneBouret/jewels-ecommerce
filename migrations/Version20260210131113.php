<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210131113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jewelry ADD CONSTRAINT FK_D884897A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE jewelry_variant ADD description LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE jewelry_variant ADD CONSTRAINT FK_99B11EFF3FB34C55 FOREIGN KEY (jewelry_id) REFERENCES jewelry (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jewelry DROP FOREIGN KEY FK_D884897A12469DE2');
        $this->addSql('ALTER TABLE jewelry_variant DROP FOREIGN KEY FK_99B11EFF3FB34C55');
        $this->addSql('ALTER TABLE jewelry_variant DROP description');
    }
}
