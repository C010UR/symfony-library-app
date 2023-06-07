<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230601220336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Books table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql(
            'CREATE TABLE book (
                id INT NOT NULL,
                publisher_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                date_published DATE NOT NULL,
                image_path VARCHAR(255) DEFAULT NULL,
                slug VARCHAR(255) NOT NULL,
                is_deleted BOOLEAN DEFAULT false NOT NULL,
                PRIMARY KEY(id)
                )'
        );

        $this->addSql('CREATE INDEX IDX_CBE5A33140C86FCE ON book (publisher_id)');

        $this->addSql(
            'CREATE TABLE book_tag (
                book_id INT NOT NULL,
                tag_id INT NOT NULL,
                PRIMARY KEY(book_id, tag_id)
                )'
        );

        $this->addSql('CREATE INDEX IDX_F2F4CE1516A2B381 ON book_tag (book_id)');
        $this->addSql('CREATE INDEX IDX_F2F4CE15BAD26311 ON book_tag (tag_id)');

        $this->addSql(
            'CREATE TABLE book_author (
            book_id INT NOT NULL,
            author_id INT NOT NULL,
            PRIMARY KEY(book_id, author_id)
            )'
        );

        $this->addSql('CREATE INDEX IDX_9478D34516A2B381 ON book_author (book_id)');
        $this->addSql('CREATE INDEX IDX_9478D345F675F31B ON book_author (author_id)');

        $this->addSql(
            'ALTER TABLE book
                ADD CONSTRAINT FK_CBE5A33140C86FCE
                FOREIGN KEY (publisher_id) REFERENCES publisher (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE book_tag
                ADD CONSTRAINT FK_F2F4CE1516A2B381
                FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE book_tag
                ADD CONSTRAINT FK_F2F4CE15BAD26311
                FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE book_author
                ADD CONSTRAINT FK_9478D34516A2B381
                FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE book_author
                ADD CONSTRAINT FK_9478D345F675F31B
                FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE book_id_seq CASCADE');
        $this->addSql('ALTER TABLE book DROP CONSTRAINT FK_CBE5A33140C86FCE');
        $this->addSql('ALTER TABLE book_tag DROP CONSTRAINT FK_F2F4CE1516A2B381');
        $this->addSql('ALTER TABLE book_tag DROP CONSTRAINT FK_F2F4CE15BAD26311');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D34516A2B381');
        $this->addSql('ALTER TABLE book_author DROP CONSTRAINT FK_9478D345F675F31B');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_tag');
        $this->addSql('DROP TABLE book_author');
    }
}
