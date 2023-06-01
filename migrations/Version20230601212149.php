<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230601212149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Book Tags table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql(
            'CREATE TABLE tag (
                id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                is_deleted BOOLEAN DEFAULT false NOT NULL,
                PRIMARY KEY(id)
                )'
        );

        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B7835E237E06 ON tag (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP TABLE tag');
    }
}
