<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230602091433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add more fields to Books table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE book ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD page_count INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE book DROP description');
        $this->addSql('ALTER TABLE book DROP page_count');
    }
}
