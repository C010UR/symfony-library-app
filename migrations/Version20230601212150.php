<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230601212150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Book Publisher table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE publisher_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql(
            'CREATE TABLE publisher (
                id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                address VARCHAR(255) NOT NULL,
                email VARCHAR(180) NOT NULL,
                website VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                is_deleted BOOLEAN DEFAULT false NOT NULL,
                PRIMARY KEY(id)
                )'
        );

        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE8D5465E237E06 ON publisher (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE8D546989D9B62 ON publisher (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE publisher_id_seq CASCADE');
        $this->addSql('DROP TABLE publisher');
    }
}
