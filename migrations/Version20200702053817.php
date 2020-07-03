<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702053817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Crear la tabla de pagos';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE pagos (
                id INT AUTO_INCREMENT NOT NULL,
                wallet_id INT NOT NULL,
                descripcion VARCHAR(100) NOT NULL,
                valor DOUBLE PRECISION NOT NULL,
                session VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                status TINYINT(1) NOT NULL,
                INDEX IDX_DA9B0DFF712520F3 (wallet_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE pagos ADD CONSTRAINT FK_DA9B0DFF712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pagos');
    }
}
