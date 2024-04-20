<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230516204044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create User table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql(
            'CREATE TABLE "user" (
                id INT GENERATED BY DEFAULT AS IDENTITY NOT NULL,
                email VARCHAR(180) NOT NULL,
                roles JSON NOT NULL,
                password VARCHAR(255) DEFAULT \'not-set\' NOT NULL,
                is_active BOOLEAN DEFAULT false NOT NULL,
                login_attempts INT DEFAULT 0 NOT NULL,
                image_path VARCHAR(255) DEFAULT NULL,
                is_deleted BOOLEAN DEFAULT false NOT NULL,
                PRIMARY KEY(id)
            )'
        );

        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE "user"');
    }
}
