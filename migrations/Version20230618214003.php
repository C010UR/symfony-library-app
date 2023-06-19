<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230618214003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE "order_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql(
            'CREATE TABLE "order" (
                id INT NOT NULL,
                user_completed_id INT DEFAULT NULL,
                book_id INT NOT NULL,
                first_name citext NOT NULL,
                last_name citext NOT NULL,
                middle_name citext DEFAULT NULL,
                phone_number citext NOT NULL,
                date_created DATE NOT NULL,
                date_completed DATE DEFAULT NULL,
                is_deleted BOOLEAN DEFAULT false NOT NULL,
                quantity INT NOT NULL,
                PRIMARY KEY(id)
                )'
        );

        $this->addSql('CREATE INDEX IDX_F52993981AC3754 ON "order" (user_completed_id)');
        $this->addSql('CREATE INDEX IDX_F529939816A2B381 ON "order" (book_id)');

        $this->addSql(
            'ALTER TABLE "order" ADD CONSTRAINT FK_F52993981AC3754
                FOREIGN KEY (user_completed_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE "order" ADD CONSTRAINT FK_F529939816A2B381
                FOREIGN KEY (book_id) REFERENCES book (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "order_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F52993981AC3754');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F529939816A2B381');
        $this->addSql('DROP TABLE "order"');
    }
}
