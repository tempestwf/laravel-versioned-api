<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180301114340 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_verification ADD user_id INT DEFAULT NULL, DROP user');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE22358A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE22358A76ED395 ON email_verification (user_id)');
        $this->addSql('ALTER TABLE users DROP email_verification');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE22358A76ED395');
        $this->addSql('DROP INDEX UNIQ_FE22358A76ED395 ON email_verification');
        $this->addSql('ALTER TABLE email_verification ADD user VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP user_id');
        $this->addSql('ALTER TABLE users ADD email_verification VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
