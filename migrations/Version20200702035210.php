<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200702035210 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Crear dos indices unicos para los clientes en el documento y el correo para evitar duplicidad de estos datos';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F41C9B25B6B12EC7 ON cliente (documento)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F41C9B25E7927C74 ON cliente (email)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_F41C9B25B6B12EC7 ON cliente');
        $this->addSql('DROP INDEX UNIQ_F41C9B25E7927C74 ON cliente');
    }
}
