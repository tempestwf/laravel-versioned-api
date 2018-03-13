<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170906013320 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums CHANGE artist_id artist_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE albums ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD time_deleted DATETIME DEFAULT NULL, ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, ADD created_from_ip VARCHAR(45) DEFAULT NULL, ADD updated_from_ip VARCHAR(45) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4E2474F5E237E06 ON albums (name)');
        $this->addSql('ALTER TABLE artists ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD time_deleted DATETIME DEFAULT NULL, ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, ADD created_from_ip VARCHAR(45) DEFAULT NULL, ADD updated_from_ip VARCHAR(45) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE permissions ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD time_deleted DATETIME DEFAULT NULL, ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, ADD created_from_ip VARCHAR(45) DEFAULT NULL, ADD updated_from_ip VARCHAR(45) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE roles ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD time_deleted DATETIME DEFAULT NULL, ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, ADD created_from_ip VARCHAR(45) DEFAULT NULL, ADD updated_from_ip VARCHAR(45) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, ADD created_from_ip VARCHAR(45) DEFAULT NULL, ADD updated_from_ip VARCHAR(45) DEFAULT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums CHANGE artist_id artist_id INT NOT NULL');

        $this->addSql('DROP INDEX UNIQ_F4E2474F5E237E06 ON albums');
        $this->addSql('ALTER TABLE albums DROP created_by, DROP updated_by, DROP time_deleted, DROP deleted, DROP created_from_ip, DROP updated_from_ip, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE artists DROP created_by, DROP updated_by, DROP time_deleted, DROP deleted, DROP created_from_ip, DROP updated_from_ip, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE permissions DROP created_by, DROP updated_by, DROP time_deleted, DROP deleted, DROP created_from_ip, DROP updated_from_ip, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE roles DROP created_by, DROP updated_by, DROP time_deleted, DROP deleted, DROP created_from_ip, DROP updated_from_ip, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE users DROP created_by, DROP updated_by, DROP created_from_ip, DROP updated_from_ip, DROP created_at, DROP updated_at');
    }
}
