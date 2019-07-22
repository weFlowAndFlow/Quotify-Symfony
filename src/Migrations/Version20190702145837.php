<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190702145837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD email VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL, ADD first_name VARCHAR(45) DEFAULT NULL, ADD last_name VARCHAR(80) DEFAULT NULL, DROP login');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4A76ED395');
        $this->addSql('DROP INDEX IDX_6B71CBF4A76ED395 ON quote');
        $this->addSql('ALTER TABLE quote DROP user_id, DROP year');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quote ADD user_id INT NOT NULL, ADD year INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF4A76ED395 ON quote (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD login VARCHAR(50) NOT NULL COLLATE utf8mb4_unicode_ci, DROP email, DROP roles, DROP first_name, DROP last_name');
    }
}
