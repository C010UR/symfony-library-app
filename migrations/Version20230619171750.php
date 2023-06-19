<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230619171750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change Email type of Publisher to citext';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE publisher ALTER email TYPE citext');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE publisher ALTER email TYPE VARCHAR(180)');
    }
}
