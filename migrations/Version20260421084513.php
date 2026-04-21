<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260421084513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, total INT NOT NULL, status VARCHAR(255) NOT NULL, purchased_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_6117D13BA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE purchase_item (id INT AUTO_INCREMENT NOT NULL, product_name VARCHAR(255) NOT NULL, product_color VARCHAR(50) NOT NULL, product_price INT NOT NULL, quantity INT NOT NULL, total INT NOT NULL, product_id INT NOT NULL, purchase_id INT NOT NULL, INDEX IDX_6FA8ED7D4584665A (product_id), INDEX IDX_6FA8ED7D558FBEB9 (purchase_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE purchase_item ADD CONSTRAINT FK_6FA8ED7D4584665A FOREIGN KEY (product_id) REFERENCES jewelry_variant (id)');
        $this->addSql('ALTER TABLE purchase_item ADD CONSTRAINT FK_6FA8ED7D558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BA76ED395');
        $this->addSql('ALTER TABLE purchase_item DROP FOREIGN KEY FK_6FA8ED7D4584665A');
        $this->addSql('ALTER TABLE purchase_item DROP FOREIGN KEY FK_6FA8ED7D558FBEB9');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE purchase_item');
    }
}
