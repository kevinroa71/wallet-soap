<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702025140 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creacion de un wallet para manejar el saldo del cliente';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE wallet (
                id INT AUTO_INCREMENT NOT NULL,
                cliente_id INT NOT NULL,
                saldo DOUBLE PRECISION NOT NULL,
                UNIQUE INDEX UNIQ_7C68921FDE734E51 (cliente_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921FDE734E51 FOREIGN KEY (cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE cliente CHANGE documento documento VARCHAR(100) NOT NULL, CHANGE nombres nombres VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE wallet');
        $this->addSql('ALTER TABLE cliente CHANGE documento documento VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nombres nombres VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
