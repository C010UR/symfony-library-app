<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230606145420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convert varchar types to citext type for better search.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE author ALTER first_name TYPE citext');
        $this->addSql('ALTER TABLE author ALTER last_name TYPE citext');
        $this->addSql('ALTER TABLE author ALTER middle_name TYPE citext');
        $this->addSql('ALTER TABLE author ALTER website TYPE citext');
        $this->addSql('ALTER TABLE author ALTER email TYPE citext');
        $this->addSql('ALTER TABLE book ALTER name TYPE citext');
        $this->addSql('ALTER TABLE book ALTER description TYPE citext');
        $this->addSql('ALTER TABLE publisher ALTER name TYPE citext');
        $this->addSql('ALTER TABLE publisher ALTER address TYPE citext');
        $this->addSql('ALTER TABLE publisher ALTER website TYPE citext');
        $this->addSql('ALTER TABLE tag ALTER name TYPE citext');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE citext');
        $this->addSql('ALTER TABLE "user" ALTER first_name TYPE citext');
        $this->addSql('ALTER TABLE "user" ALTER last_name TYPE citext');
        $this->addSql('ALTER TABLE "user" ALTER middle_name TYPE citext');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tag ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE author ALTER first_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE author ALTER last_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE author ALTER middle_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE author ALTER website TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE author ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(180)');
        $this->addSql('ALTER TABLE "user" ALTER first_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER last_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER middle_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE book ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE book ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE publisher ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE publisher ALTER address TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE publisher ALTER website TYPE VARCHAR(255)');
    }
}
