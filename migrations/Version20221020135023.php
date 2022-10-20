<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221020135023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, title, content, update_at, author, nb_views, published, create_at, slug FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, update_at DATETIME NOT NULL, author VARCHAR(128) NOT NULL, nb_views INTEGER NOT NULL, published BOOLEAN NOT NULL, create_at DATETIME NOT NULL, slug VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO article (id, title, content, update_at, author, nb_views, published, create_at, slug) SELECT id, title, content, update_at, author, nb_views, published, create_at, slug FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E66989D9B62 ON article (slug)');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, title, content, create_at, update_at, author, nb_views, published, slug FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, update_at DATETIME NOT NULL, author VARCHAR(128) NOT NULL, nb_views INTEGER NOT NULL, published BOOLEAN NOT NULL, slug VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO article (id, title, content, create_at, update_at, author, nb_views, published, slug) SELECT id, title, content, create_at, update_at, author, nb_views, published, slug FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E66989D9B62 ON article (slug)');
    }
}
