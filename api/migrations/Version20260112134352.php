<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260112134352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tool (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, vendor VARCHAR(100) NOT NULL, website_url VARCHAR(255) DEFAULT NULL, monthly_cost NUMERIC(10, 2) NOT NULL, owner_department VARCHAR(50) NOT NULL, status VARCHAR(20) NOT NULL, active_users_count INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INT NOT NULL, INDEX IDX_20F33ED112469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE tool ADD CONSTRAINT FK_20F33ED112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE access_requests DROP FOREIGN KEY `access_requests_ibfk_1`');
        $this->addSql('ALTER TABLE access_requests DROP FOREIGN KEY `access_requests_ibfk_2`');
        $this->addSql('ALTER TABLE access_requests DROP FOREIGN KEY `access_requests_ibfk_3`');
        $this->addSql('ALTER TABLE cost_tracking DROP FOREIGN KEY `cost_tracking_ibfk_1`');
        $this->addSql('ALTER TABLE tools DROP FOREIGN KEY `tools_ibfk_1`');
        $this->addSql('ALTER TABLE usage_logs DROP FOREIGN KEY `usage_logs_ibfk_1`');
        $this->addSql('ALTER TABLE usage_logs DROP FOREIGN KEY `usage_logs_ibfk_2`');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY `user_tool_access_ibfk_1`');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY `user_tool_access_ibfk_2`');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY `user_tool_access_ibfk_3`');
        $this->addSql('ALTER TABLE user_tool_access DROP FOREIGN KEY `user_tool_access_ibfk_4`');
        $this->addSql('DROP TABLE access_requests');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE cost_tracking');
        $this->addSql('DROP TABLE tools');
        $this->addSql('DROP TABLE usage_logs');
        $this->addSql('DROP TABLE user_tool_access');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_requests (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tool_id INT NOT NULL, business_justification TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, status ENUM(\'pending\', \'approved\', \'rejected\') CHARACTER SET utf8mb4 DEFAULT \'pending\' COLLATE `utf8mb4_0900_ai_ci`, requested_at DATETIME DEFAULT CURRENT_TIMESTAMP, processed_at DATETIME DEFAULT NULL, processed_by INT DEFAULT NULL, processing_notes TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, INDEX idx_requests_date (requested_at), INDEX idx_requests_user (user_id), INDEX idx_requests_status (status), INDEX processed_by (processed_by), INDEX tool_id (tool_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, color_hex VARCHAR(7) CHARACTER SET utf8mb4 DEFAULT \'#6366f1\' COLLATE `utf8mb4_0900_ai_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, UNIQUE INDEX name (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE cost_tracking (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, month_year DATE NOT NULL, total_monthly_cost NUMERIC(10, 2) NOT NULL, active_users_count INT DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_cost_month_tool (month_year, tool_id), UNIQUE INDEX unique_tool_month (tool_id, month_year), INDEX IDX_1E5C21A98F7B22CC (tool_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tools (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, description TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, vendor VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, website_url VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, category_id INT NOT NULL, monthly_cost NUMERIC(10, 2) NOT NULL, active_users_count INT DEFAULT 0 NOT NULL, owner_department ENUM(\'Engineering\', \'Sales\', \'Marketing\', \'HR\', \'Finance\', \'Operations\', \'Design\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, status ENUM(\'active\', \'deprecated\', \'trial\') CHARACTER SET utf8mb4 DEFAULT \'active\' COLLATE `utf8mb4_0900_ai_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_tools_department (owner_department), INDEX idx_tools_cost_desc (monthly_cost), INDEX idx_tools_status (status), INDEX idx_tools_active_users (active_users_count), INDEX idx_tools_category (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE usage_logs (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tool_id INT NOT NULL, session_date DATE NOT NULL, usage_minutes INT DEFAULT 0, actions_count INT DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX tool_id (tool_id), INDEX idx_usage_date_tool (session_date, tool_id), INDEX idx_usage_user_date (user_id, session_date), INDEX IDX_5B25D447A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_tool_access (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tool_id INT NOT NULL, granted_at DATETIME DEFAULT CURRENT_TIMESTAMP, granted_by INT NOT NULL, revoked_at DATETIME DEFAULT NULL, revoked_by INT DEFAULT NULL, status ENUM(\'active\', \'revoked\') CHARACTER SET utf8mb4 DEFAULT \'active\' COLLATE `utf8mb4_0900_ai_ci`, INDEX idx_access_granted_date (granted_at), INDEX idx_access_status (status), INDEX idx_access_tool (tool_id), INDEX idx_access_user (user_id), INDEX revoked_by (revoked_by), INDEX granted_by (granted_by), UNIQUE INDEX unique_user_tool_active (user_id, tool_id, status), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, email VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, department ENUM(\'Engineering\', \'Sales\', \'Marketing\', \'HR\', \'Finance\', \'Operations\', \'Design\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, role ENUM(\'employee\', \'manager\', \'admin\') CHARACTER SET utf8mb4 DEFAULT \'employee\' COLLATE `utf8mb4_0900_ai_ci`, status ENUM(\'active\', \'inactive\') CHARACTER SET utf8mb4 DEFAULT \'active\' COLLATE `utf8mb4_0900_ai_ci`, hire_date DATE DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_users_status (status), INDEX idx_users_department (department), UNIQUE INDEX email (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE access_requests ADD CONSTRAINT `access_requests_ibfk_1` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE access_requests ADD CONSTRAINT `access_requests_ibfk_2` FOREIGN KEY (tool_id) REFERENCES tools (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE access_requests ADD CONSTRAINT `access_requests_ibfk_3` FOREIGN KEY (processed_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cost_tracking ADD CONSTRAINT `cost_tracking_ibfk_1` FOREIGN KEY (tool_id) REFERENCES tools (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tools ADD CONSTRAINT `tools_ibfk_1` FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE NO ACTION');
        $this->addSql('ALTER TABLE usage_logs ADD CONSTRAINT `usage_logs_ibfk_1` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE usage_logs ADD CONSTRAINT `usage_logs_ibfk_2` FOREIGN KEY (tool_id) REFERENCES tools (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT `user_tool_access_ibfk_1` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT `user_tool_access_ibfk_2` FOREIGN KEY (tool_id) REFERENCES tools (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT `user_tool_access_ibfk_3` FOREIGN KEY (granted_by) REFERENCES users (id) ON UPDATE NO ACTION');
        $this->addSql('ALTER TABLE user_tool_access ADD CONSTRAINT `user_tool_access_ibfk_4` FOREIGN KEY (revoked_by) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE tool DROP FOREIGN KEY FK_20F33ED112469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE tool');
    }
}
