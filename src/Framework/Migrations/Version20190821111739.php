<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190821111739 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE station (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, province VARCHAR(255) NOT NULL, municipality VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, postal_code VARCHAR(5) NOT NULL, address VARCHAR(255) NOT NULL, lng DOUBLE PRECISION NOT NULL, lat DOUBLE PRECISION NOT NULL, label VARCHAR(255) NOT NULL, rem VARCHAR(5) NOT NULL, sale_type VARCHAR(5) NOT NULL, schedule VARCHAR(255) NOT NULL, price_gasoline_95 DOUBLE PRECISION DEFAULT NULL, price_diesel_a DOUBLE PRECISION DEFAULT NULL, price_diesel_b DOUBLE PRECISION DEFAULT NULL, price_bioethanol DOUBLE PRECISION DEFAULT NULL, price_new_diesel_a DOUBLE PRECISION DEFAULT NULL, price_biodiesel DOUBLE PRECISION DEFAULT NULL, price_gasoline_98 DOUBLE PRECISION DEFAULT NULL, price_compressed_natural_gas DOUBLE PRECISION DEFAULT NULL, price_liquefied_natural_gas DOUBLE PRECISION DEFAULT NULL, price_liquefied_petroleum_gas DOUBLE PRECISION DEFAULT NULL, state SMALLINT NOT NULL, INDEX IDX_9F39F8B193CB796C (file_id), INDEX state_idx (state), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(25) NOT NULL, file VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B193CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B193CB796C');
        $this->addSql('DROP TABLE station');
        $this->addSql('DROP TABLE file');
    }
}
