<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20170621013435 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE permissions CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE organisations CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE roles CHANGE name name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE job job VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE organisations CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE permissions CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE roles CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE address address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE job job VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
