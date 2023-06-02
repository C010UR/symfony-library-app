<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230602102730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug fields to Tag and User tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tag ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD slug VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP slug');
        $this->addSql('ALTER TABLE tag DROP slug');
    }
}
