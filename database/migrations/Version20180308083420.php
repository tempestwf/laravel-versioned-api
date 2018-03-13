<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180308083420 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums DROP deleted, CHANGE time_deleted deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE artists DROP deleted, CHANGE time_deleted deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE email_verification DROP deleted, CHANGE time_deleted deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE permissions DROP deleted, CHANGE time_deleted deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE roles DROP deleted, CHANGE time_deleted deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE socialize_user DROP deleted, CHANGE time_deleted deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP deleted, CHANGE time_deleted deleted_at DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at time_deleted DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE artists ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at time_deleted DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE email_verification ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at time_deleted DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE permissions ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at time_deleted DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE roles ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at time_deleted DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE socialize_user ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at time_deleted DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at time_deleted DATETIME DEFAULT NULL');
    }
}
