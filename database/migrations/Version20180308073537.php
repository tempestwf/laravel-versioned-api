<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180308073537 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474F16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4E2474FDE12AB56 ON albums (created_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4E2474F16FE72E1 ON albums (updated_by)');
        $this->addSql('ALTER TABLE artists CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE artists ADD CONSTRAINT FK_68D3801EDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE artists ADD CONSTRAINT FK_68D3801E16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68D3801EDE12AB56 ON artists (created_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_68D3801E16FE72E1 ON artists (updated_by)');
        $this->addSql('ALTER TABLE email_verification CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE22358DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE2235816FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE22358DE12AB56 ON email_verification (created_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE2235816FE72E1 ON email_verification (updated_by)');
        $this->addSql('ALTER TABLE permissions CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6FDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6F16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DEDCC6FDE12AB56 ON permissions (created_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DEDCC6F16FE72E1 ON permissions (updated_by)');
        $this->addSql('ALTER TABLE roles CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B63E2EC7DE12AB56 ON roles (created_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B63E2EC716FE72E1 ON roles (updated_by)');
        $this->addSql('ALTER TABLE socialize_user CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE socialize_user ADD CONSTRAINT FK_570010DADE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE socialize_user ADD CONSTRAINT FK_570010DA16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_570010DADE12AB56 ON socialize_user (created_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_570010DA16FE72E1 ON socialize_user (updated_by)');
        $this->addSql('ALTER TABLE users CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E916FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9DE12AB56 ON users (created_by)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E916FE72E1 ON users (updated_by)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474FDE12AB56');
        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474F16FE72E1');
        $this->addSql('DROP INDEX UNIQ_F4E2474FDE12AB56 ON albums');
        $this->addSql('DROP INDEX UNIQ_F4E2474F16FE72E1 ON albums');
        $this->addSql('ALTER TABLE albums CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE artists DROP FOREIGN KEY FK_68D3801EDE12AB56');
        $this->addSql('ALTER TABLE artists DROP FOREIGN KEY FK_68D3801E16FE72E1');
        $this->addSql('DROP INDEX UNIQ_68D3801EDE12AB56 ON artists');
        $this->addSql('DROP INDEX UNIQ_68D3801E16FE72E1 ON artists');
        $this->addSql('ALTER TABLE artists CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE22358DE12AB56');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE2235816FE72E1');
        $this->addSql('DROP INDEX UNIQ_FE22358DE12AB56 ON email_verification');
        $this->addSql('DROP INDEX UNIQ_FE2235816FE72E1 ON email_verification');
        $this->addSql('ALTER TABLE email_verification CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE permissions DROP FOREIGN KEY FK_2DEDCC6FDE12AB56');
        $this->addSql('ALTER TABLE permissions DROP FOREIGN KEY FK_2DEDCC6F16FE72E1');
        $this->addSql('DROP INDEX UNIQ_2DEDCC6FDE12AB56 ON permissions');
        $this->addSql('DROP INDEX UNIQ_2DEDCC6F16FE72E1 ON permissions');
        $this->addSql('ALTER TABLE permissions CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC7DE12AB56');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC716FE72E1');
        $this->addSql('DROP INDEX UNIQ_B63E2EC7DE12AB56 ON roles');
        $this->addSql('DROP INDEX UNIQ_B63E2EC716FE72E1 ON roles');
        $this->addSql('ALTER TABLE roles CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE socialize_user DROP FOREIGN KEY FK_570010DADE12AB56');
        $this->addSql('ALTER TABLE socialize_user DROP FOREIGN KEY FK_570010DA16FE72E1');
        $this->addSql('DROP INDEX UNIQ_570010DADE12AB56 ON socialize_user');
        $this->addSql('DROP INDEX UNIQ_570010DA16FE72E1 ON socialize_user');
        $this->addSql('ALTER TABLE socialize_user CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9DE12AB56');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E916FE72E1');
        $this->addSql('DROP INDEX UNIQ_1483A5E9DE12AB56 ON users');
        $this->addSql('DROP INDEX UNIQ_1483A5E916FE72E1 ON users');
        $this->addSql('ALTER TABLE users CHANGE created_by created_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
