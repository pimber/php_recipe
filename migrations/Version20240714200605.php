<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240714200605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ingredient_amount_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ingredient_amount_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE recipe_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ingredient (id INT NOT NULL, name VARCHAR(60) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ingredient_amount (id INT NOT NULL, ingredient_id_id INT NOT NULL, recipe_id_id INT NOT NULL, ingredient_amount_type_id_id INT NOT NULL, amount NUMERIC(6, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_62DBB0036676F996 ON ingredient_amount (ingredient_id_id)');
        $this->addSql('CREATE INDEX IDX_62DBB00369574A48 ON ingredient_amount (recipe_id_id)');
        $this->addSql('CREATE INDEX IDX_62DBB00318B7CC2 ON ingredient_amount (ingredient_amount_type_id_id)');
        $this->addSql('CREATE TABLE ingredient_amount_type (id INT NOT NULL, type VARCHAR(60) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE recipe (id INT NOT NULL, user_id_id INT NOT NULL, name VARCHAR(60) NOT NULL, prep_time_hour INT NOT NULL, prep_time_min INT NOT NULL, cook_time_hour INT NOT NULL, cook_time_min INT NOT NULL, description VARCHAR(255) NOT NULL, created_on DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DA88B1379D86650F ON recipe (user_id_id)');
        $this->addSql('ALTER TABLE ingredient_amount ADD CONSTRAINT FK_62DBB0036676F996 FOREIGN KEY (ingredient_id_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient_amount ADD CONSTRAINT FK_62DBB00369574A48 FOREIGN KEY (recipe_id_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient_amount ADD CONSTRAINT FK_62DBB00318B7CC2 FOREIGN KEY (ingredient_amount_type_id_id) REFERENCES ingredient_amount_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B1379D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE ingredient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ingredient_amount_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ingredient_amount_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE recipe_id_seq CASCADE');
        $this->addSql('ALTER TABLE ingredient_amount DROP CONSTRAINT FK_62DBB0036676F996');
        $this->addSql('ALTER TABLE ingredient_amount DROP CONSTRAINT FK_62DBB00369574A48');
        $this->addSql('ALTER TABLE ingredient_amount DROP CONSTRAINT FK_62DBB00318B7CC2');
        $this->addSql('ALTER TABLE recipe DROP CONSTRAINT FK_DA88B1379D86650F');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE ingredient_amount');
        $this->addSql('DROP TABLE ingredient_amount_type');
        $this->addSql('DROP TABLE recipe');
    }
}
