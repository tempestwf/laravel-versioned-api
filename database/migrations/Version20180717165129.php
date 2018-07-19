<?php

namespace Database\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema as Schema;

class Version20180717165129 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE albums (id INT AUTO_INCREMENT NOT NULL, artist_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, release_date DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_F4E2474F5E237E06 (name), INDEX IDX_F4E2474FB7970CF8 (artist_id), INDEX IDX_F4E2474FDE12AB56 (created_by), INDEX IDX_F4E2474F16FE72E1 (updated_by), INDEX name_idx (name), INDEX releaseDate_idx (release_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artists (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_68D3801EDE12AB56 (created_by), INDEX IDX_68D3801E16FE72E1 (updated_by), UNIQUE INDEX name_unq (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE email_verification (id VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, verified TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_FE22358A76ED395 (user_id), INDEX IDX_FE22358DE12AB56 (created_by), INDEX IDX_FE2235816FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE login_attempts (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, count INT DEFAULT NULL, full_lock_count INT DEFAULT NULL, trace LONGTEXT DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9163C7FBDE12AB56 (created_by), INDEX IDX_9163C7FB16FE72E1 (updated_by), UNIQUE INDEX login_attempt_user_unq (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_reset (id VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, verified TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B1017252A76ED395 (user_id), INDEX IDX_B1017252DE12AB56 (created_by), INDEX IDX_B101725216FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2DEDCC6FDE12AB56 (created_by), INDEX IDX_2DEDCC6F16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE UserToPermission (permission_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2B833CF8FED90CCA (permission_id), INDEX IDX_2B833CF8A76ED395 (user_id), PRIMARY KEY(permission_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE RoleToPermission (permission_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_B0CFADE4FED90CCA (permission_id), INDEX IDX_B0CFADE4D60322AC (role_id), PRIMARY KEY(permission_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B63E2EC7DE12AB56 (created_by), INDEX IDX_B63E2EC716FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE UserToRole (role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CC07E582D60322AC (role_id), INDEX IDX_CC07E582A76ED395 (user_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE socialize_user (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, socialize_id VARCHAR(255) DEFAULT NULL, nickname VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, avatar_original VARCHAR(255) DEFAULT NULL, profile_url VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, refresh_token VARCHAR(255) DEFAULT NULL, expires_in INT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_570010DAA76ED395 (user_id), INDEX IDX_570010DADE12AB56 (created_by), INDEX IDX_570010DA16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, locale VARCHAR(255) DEFAULT \'en\' NOT NULL, job VARCHAR(255) DEFAULT NULL, locked TINYINT(1) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9989D9B62 (slug), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E9DE12AB56 (created_by), INDEX IDX_1483A5E916FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE AlbumToUser (user_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_4B4684D8A76ED395 (user_id), INDEX IDX_4B4684D81137ABCF (album_id), PRIMARY KEY(user_id, album_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FB7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474FDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE albums ADD CONSTRAINT FK_F4E2474F16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE artists ADD CONSTRAINT FK_68D3801EDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE artists ADD CONSTRAINT FK_68D3801E16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE22358A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE22358DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE email_verification ADD CONSTRAINT FK_FE2235816FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE login_attempts ADD CONSTRAINT FK_9163C7FBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE login_attempts ADD CONSTRAINT FK_9163C7FBDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE login_attempts ADD CONSTRAINT FK_9163C7FB16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE password_reset ADD CONSTRAINT FK_B1017252A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE password_reset ADD CONSTRAINT FK_B1017252DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE password_reset ADD CONSTRAINT FK_B101725216FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6FDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE permissions ADD CONSTRAINT FK_2DEDCC6F16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE UserToPermission ADD CONSTRAINT FK_2B833CF8FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE UserToPermission ADD CONSTRAINT FK_2B833CF8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE RoleToPermission ADD CONSTRAINT FK_B0CFADE4FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE RoleToPermission ADD CONSTRAINT FK_B0CFADE4D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE UserToRole ADD CONSTRAINT FK_CC07E582D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE UserToRole ADD CONSTRAINT FK_CC07E582A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE socialize_user ADD CONSTRAINT FK_570010DAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE socialize_user ADD CONSTRAINT FK_570010DADE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE socialize_user ADD CONSTRAINT FK_570010DA16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E916FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE AlbumToUser ADD CONSTRAINT FK_4B4684D8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE AlbumToUser ADD CONSTRAINT FK_4B4684D81137ABCF FOREIGN KEY (album_id) REFERENCES albums (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE AlbumToUser DROP FOREIGN KEY FK_4B4684D81137ABCF');
        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474FB7970CF8');
        $this->addSql('ALTER TABLE UserToPermission DROP FOREIGN KEY FK_2B833CF8FED90CCA');
        $this->addSql('ALTER TABLE RoleToPermission DROP FOREIGN KEY FK_B0CFADE4FED90CCA');
        $this->addSql('ALTER TABLE RoleToPermission DROP FOREIGN KEY FK_B0CFADE4D60322AC');
        $this->addSql('ALTER TABLE UserToRole DROP FOREIGN KEY FK_CC07E582D60322AC');
        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474FDE12AB56');
        $this->addSql('ALTER TABLE albums DROP FOREIGN KEY FK_F4E2474F16FE72E1');
        $this->addSql('ALTER TABLE artists DROP FOREIGN KEY FK_68D3801EDE12AB56');
        $this->addSql('ALTER TABLE artists DROP FOREIGN KEY FK_68D3801E16FE72E1');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE22358A76ED395');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE22358DE12AB56');
        $this->addSql('ALTER TABLE email_verification DROP FOREIGN KEY FK_FE2235816FE72E1');
        $this->addSql('ALTER TABLE login_attempts DROP FOREIGN KEY FK_9163C7FBA76ED395');
        $this->addSql('ALTER TABLE login_attempts DROP FOREIGN KEY FK_9163C7FBDE12AB56');
        $this->addSql('ALTER TABLE login_attempts DROP FOREIGN KEY FK_9163C7FB16FE72E1');
        $this->addSql('ALTER TABLE password_reset DROP FOREIGN KEY FK_B1017252A76ED395');
        $this->addSql('ALTER TABLE password_reset DROP FOREIGN KEY FK_B1017252DE12AB56');
        $this->addSql('ALTER TABLE password_reset DROP FOREIGN KEY FK_B101725216FE72E1');
        $this->addSql('ALTER TABLE permissions DROP FOREIGN KEY FK_2DEDCC6FDE12AB56');
        $this->addSql('ALTER TABLE permissions DROP FOREIGN KEY FK_2DEDCC6F16FE72E1');
        $this->addSql('ALTER TABLE UserToPermission DROP FOREIGN KEY FK_2B833CF8A76ED395');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC7DE12AB56');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC716FE72E1');
        $this->addSql('ALTER TABLE UserToRole DROP FOREIGN KEY FK_CC07E582A76ED395');
        $this->addSql('ALTER TABLE socialize_user DROP FOREIGN KEY FK_570010DAA76ED395');
        $this->addSql('ALTER TABLE socialize_user DROP FOREIGN KEY FK_570010DADE12AB56');
        $this->addSql('ALTER TABLE socialize_user DROP FOREIGN KEY FK_570010DA16FE72E1');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9DE12AB56');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E916FE72E1');
        $this->addSql('ALTER TABLE AlbumToUser DROP FOREIGN KEY FK_4B4684D8A76ED395');
        $this->addSql('DROP TABLE albums');
        $this->addSql('DROP TABLE artists');
        $this->addSql('DROP TABLE email_verification');
        $this->addSql('DROP TABLE login_attempts');
        $this->addSql('DROP TABLE password_reset');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE UserToPermission');
        $this->addSql('DROP TABLE RoleToPermission');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE UserToRole');
        $this->addSql('DROP TABLE socialize_user');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE AlbumToUser');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}
