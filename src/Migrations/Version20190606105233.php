<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190606105233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE original_work_author (original_work_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_E159FEC03DB4FD2C (original_work_id), INDEX IDX_E159FEC0F675F31B (author_id), PRIMARY KEY(original_work_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quote_author (quote_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_8717768BDB805178 (quote_id), INDEX IDX_8717768BF675F31B (author_id), PRIMARY KEY(quote_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE original_work_author ADD CONSTRAINT FK_E159FEC03DB4FD2C FOREIGN KEY (original_work_id) REFERENCES original_work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE original_work_author ADD CONSTRAINT FK_E159FEC0F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quote_author ADD CONSTRAINT FK_8717768BDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quote_author ADD CONSTRAINT FK_8717768BF675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quote ADD user_id INT NOT NULL, ADD category_id INT DEFAULT NULL, ADD original_work_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF412469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF43DB4FD2C FOREIGN KEY (original_work_id) REFERENCES original_work (id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF4A76ED395 ON quote (user_id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF412469DE2 ON quote (category_id)');
        $this->addSql('CREATE INDEX IDX_6B71CBF43DB4FD2C ON quote (original_work_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE original_work_author');
        $this->addSql('DROP TABLE quote_author');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4A76ED395');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF412469DE2');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF43DB4FD2C');
        $this->addSql('DROP INDEX IDX_6B71CBF4A76ED395 ON quote');
        $this->addSql('DROP INDEX IDX_6B71CBF412469DE2 ON quote');
        $this->addSql('DROP INDEX IDX_6B71CBF43DB4FD2C ON quote');
        $this->addSql('ALTER TABLE quote DROP user_id, DROP category_id, DROP original_work_id');
    }
}
