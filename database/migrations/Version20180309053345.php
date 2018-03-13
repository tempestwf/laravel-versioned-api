<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180309053345 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums DROP INDEX UNIQ_F4E2474FDE12AB56, ADD INDEX IDX_F4E2474FDE12AB56 (created_by)');
        $this->addSql('ALTER TABLE albums DROP INDEX UNIQ_F4E2474F16FE72E1, ADD INDEX IDX_F4E2474F16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE artists DROP INDEX UNIQ_68D3801EDE12AB56, ADD INDEX IDX_68D3801EDE12AB56 (created_by)');
        $this->addSql('ALTER TABLE artists DROP INDEX UNIQ_68D3801E16FE72E1, ADD INDEX IDX_68D3801E16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE email_verification DROP INDEX UNIQ_FE22358DE12AB56, ADD INDEX IDX_FE22358DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE email_verification DROP INDEX UNIQ_FE2235816FE72E1, ADD INDEX IDX_FE2235816FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE permissions DROP INDEX UNIQ_2DEDCC6FDE12AB56, ADD INDEX IDX_2DEDCC6FDE12AB56 (created_by)');
        $this->addSql('ALTER TABLE permissions DROP INDEX UNIQ_2DEDCC6F16FE72E1, ADD INDEX IDX_2DEDCC6F16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE roles DROP INDEX UNIQ_B63E2EC7DE12AB56, ADD INDEX IDX_B63E2EC7DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE roles DROP INDEX UNIQ_B63E2EC716FE72E1, ADD INDEX IDX_B63E2EC716FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE socialize_user DROP INDEX UNIQ_570010DADE12AB56, ADD INDEX IDX_570010DADE12AB56 (created_by)');
        $this->addSql('ALTER TABLE socialize_user DROP INDEX UNIQ_570010DA16FE72E1, ADD INDEX IDX_570010DA16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE users DROP INDEX UNIQ_1483A5E9DE12AB56, ADD INDEX IDX_1483A5E9DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE users DROP INDEX UNIQ_1483A5E916FE72E1, ADD INDEX IDX_1483A5E916FE72E1 (updated_by)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE albums DROP INDEX IDX_F4E2474FDE12AB56, ADD UNIQUE INDEX UNIQ_F4E2474FDE12AB56 (created_by)');
        $this->addSql('ALTER TABLE albums DROP INDEX IDX_F4E2474F16FE72E1, ADD UNIQUE INDEX UNIQ_F4E2474F16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE artists DROP INDEX IDX_68D3801EDE12AB56, ADD UNIQUE INDEX UNIQ_68D3801EDE12AB56 (created_by)');
        $this->addSql('ALTER TABLE artists DROP INDEX IDX_68D3801E16FE72E1, ADD UNIQUE INDEX UNIQ_68D3801E16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE email_verification DROP INDEX IDX_FE22358DE12AB56, ADD UNIQUE INDEX UNIQ_FE22358DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE email_verification DROP INDEX IDX_FE2235816FE72E1, ADD UNIQUE INDEX UNIQ_FE2235816FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE permissions DROP INDEX IDX_2DEDCC6FDE12AB56, ADD UNIQUE INDEX UNIQ_2DEDCC6FDE12AB56 (created_by)');
        $this->addSql('ALTER TABLE permissions DROP INDEX IDX_2DEDCC6F16FE72E1, ADD UNIQUE INDEX UNIQ_2DEDCC6F16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE roles DROP INDEX IDX_B63E2EC7DE12AB56, ADD UNIQUE INDEX UNIQ_B63E2EC7DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE roles DROP INDEX IDX_B63E2EC716FE72E1, ADD UNIQUE INDEX UNIQ_B63E2EC716FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE socialize_user DROP INDEX IDX_570010DADE12AB56, ADD UNIQUE INDEX UNIQ_570010DADE12AB56 (created_by)');
        $this->addSql('ALTER TABLE socialize_user DROP INDEX IDX_570010DA16FE72E1, ADD UNIQUE INDEX UNIQ_570010DA16FE72E1 (updated_by)');
        $this->addSql('ALTER TABLE users DROP INDEX IDX_1483A5E9DE12AB56, ADD UNIQUE INDEX UNIQ_1483A5E9DE12AB56 (created_by)');
        $this->addSql('ALTER TABLE users DROP INDEX IDX_1483A5E916FE72E1, ADD UNIQUE INDEX UNIQ_1483A5E916FE72E1 (updated_by)');
    }
}
