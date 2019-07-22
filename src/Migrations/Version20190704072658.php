<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190704072658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1A76ED395 ON category (user_id)');
        $this->addSql('ALTER TABLE original_work ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE original_work ADD CONSTRAINT FK_593B03B6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_593B03B6A76ED395 ON original_work (user_id)');
        $this->addSql('ALTER TABLE quote ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF4A76ED395 ON quote (user_id)');
        $this->addSql('ALTER TABLE author ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE author ADD CONSTRAINT FK_BDAFD8C8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_BDAFD8C8A76ED395 ON author (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE author DROP FOREIGN KEY FK_BDAFD8C8A76ED395');
        $this->addSql('DROP INDEX IDX_BDAFD8C8A76ED395 ON author');
        $this->addSql('ALTER TABLE author DROP user_id');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A76ED395');
        $this->addSql('DROP INDEX IDX_64C19C1A76ED395 ON category');
        $this->addSql('ALTER TABLE category DROP user_id');
        $this->addSql('ALTER TABLE original_work DROP FOREIGN KEY FK_593B03B6A76ED395');
        $this->addSql('DROP INDEX IDX_593B03B6A76ED395 ON original_work');
        $this->addSql('ALTER TABLE original_work DROP user_id');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4A76ED395');
        $this->addSql('DROP INDEX IDX_6B71CBF4A76ED395 ON quote');
        $this->addSql('ALTER TABLE quote DROP user_id');
    }
}
