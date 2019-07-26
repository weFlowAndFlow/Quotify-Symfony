<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190624133237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE original_work (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, year INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE original_work_author (original_work_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_E159FEC03DB4FD2C (original_work_id), INDEX IDX_E159FEC0F675F31B (author_id), PRIMARY KEY(original_work_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, login VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, author_id INT DEFAULT NULL, original_work_id INT DEFAULT NULL, text LONGTEXT NOT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_6B71CBF4A76ED395 (user_id), INDEX IDX_6B71CBF4F675F31B (author_id), INDEX IDX_6B71CBF43DB4FD2C (original_work_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quote_category (quote_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_EC76B150DB805178 (quote_id), INDEX IDX_EC76B15012469DE2 (category_id), PRIMARY KEY(quote_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, forename VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE original_work_author ADD CONSTRAINT FK_E159FEC03DB4FD2C FOREIGN KEY (original_work_id) REFERENCES original_work (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE original_work_author ADD CONSTRAINT FK_E159FEC0F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF43DB4FD2C FOREIGN KEY (original_work_id) REFERENCES original_work (id)');
        $this->addSql('ALTER TABLE quote_category ADD CONSTRAINT FK_EC76B150DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quote_category ADD CONSTRAINT FK_EC76B15012469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE quote_category DROP FOREIGN KEY FK_EC76B15012469DE2');
        $this->addSql('ALTER TABLE original_work_author DROP FOREIGN KEY FK_E159FEC03DB4FD2C');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF43DB4FD2C');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4A76ED395');
        $this->addSql('ALTER TABLE quote_category DROP FOREIGN KEY FK_EC76B150DB805178');
        $this->addSql('ALTER TABLE original_work_author DROP FOREIGN KEY FK_E159FEC0F675F31B');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4F675F31B');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE original_work');
        $this->addSql('DROP TABLE original_work_author');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE quote_category');
        $this->addSql('DROP TABLE author');
    }
}
