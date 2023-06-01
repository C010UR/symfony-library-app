<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230601214647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Book Authors table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE author_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql(
            'CREATE TABLE author (
                id INT NOT NULL,
                first_name VARCHAR(255) NOT NULL,
                last_name VARCHAR(255) NOT NULL,
                middle_name VARCHAR(255) DEFAULT NULL,
                website VARCHAR(255) DEFAULT NULL,
                slug VARCHAR(255) NOT NULL,
                is_deleted BOOLEAN DEFAULT false NOT NULL,
                email VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY(id)
                )'
        );

        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDAFD8C8989D9B62 ON author (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BDAFD8C8A9D1C132C808BA5A59107AF8 ON author (first_name, last_name, middle_name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE author_id_seq CASCADE');
        $this->addSql('DROP TABLE author');
    }
}
