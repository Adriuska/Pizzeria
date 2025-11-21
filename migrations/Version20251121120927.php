<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121120927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Pizza Nova database schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), UNIQUE INDEX UNIQ_8D93D649F37A13B (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pizza (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, ingredients LONGTEXT NOT NULL, sizes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', image_url VARCHAR(255) DEFAULT NULL, category VARCHAR(100) NOT NULL, is_available TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, status VARCHAR(50) NOT NULL, subtotal_amount NUMERIC(10, 2) DEFAULT NULL, tax_amount NUMERIC(10, 2) DEFAULT NULL, total_amount NUMERIC(10, 2) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, estimated_delivery_time DATETIME DEFAULT NULL, INDEX IDX_F5299398A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_relation_id INT NOT NULL, pizza_id INT NOT NULL, pizza_name VARCHAR(255) NOT NULL, size VARCHAR(50) NOT NULL, quantity INT NOT NULL, unit_price NUMERIC(10, 2) NOT NULL, item_subtotal NUMERIC(10, 2) NOT NULL, tax_rate NUMERIC(5, 2) NOT NULL, tax_amount NUMERIC(10, 2) NOT NULL, item_total NUMERIC(10, 2) NOT NULL, INDEX IDX_52EA1F092F4C0C95 (order_relation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F092F4C0C95 FOREIGN KEY (order_relation_id) REFERENCES `order` (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F092F4C0C95');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE pizza');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_item');
    }
}
