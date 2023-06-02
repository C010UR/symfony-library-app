<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230602090504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image fields to Publishers and Authors tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE author ADD image_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE publisher ADD image_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE publisher DROP image_path');
        $this->addSql('ALTER TABLE author DROP image_path');
    }
}
