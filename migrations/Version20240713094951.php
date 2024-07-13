<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240713094951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE user_information DROP CONSTRAINT fk_users_id');
        $this->addSql('ALTER TABLE recipe DROP CONSTRAINT fk_users_information_id');
        $this->addSql('ALTER TABLE ingredient_amount DROP CONSTRAINT fk_ingredients');
        $this->addSql('ALTER TABLE ingredient_amount DROP CONSTRAINT fk_recipe');
        $this->addSql('ALTER TABLE ingredient_amount DROP CONSTRAINT fk_ingredient_amount_type');
        $this->addSql('DROP TABLE user_information');
        $this->addSql('DROP TABLE ingredient_amount_type');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE ingredient_amount');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT "user_pkey"');
        $this->addSql('ALTER TABLE "user" ALTER name TYPE VARCHAR(60)');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(60)');
        $this->addSql('ALTER TABLE "user" ALTER role TYPE JSON');
        $this->addSql('ALTER TABLE "user" ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE "user" ALTER role SET NOT NULL');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN user_id TO id');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('CREATE TABLE user_information (user_information_id INT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(user_information_id))');
        $this->addSql('CREATE INDEX IDX_8062D116A76ED395 ON user_information (user_id)');
        $this->addSql('CREATE TABLE ingredient_amount_type (ingredient_amount_type_id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(ingredient_amount_type_id))');
        $this->addSql('CREATE TABLE recipe (recipe_id INT NOT NULL, user_information_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, prep_time_hour INT NOT NULL, prep_time_min INT NOT NULL, cook_time_hour INT NOT NULL, cook_time_min INT NOT NULL, description TEXT NOT NULL, created_on TEXT DEFAULT NULL, PRIMARY KEY(recipe_id))');
        $this->addSql('CREATE INDEX IDX_DA88B1374575EE58 ON recipe (user_information_id)');
        $this->addSql('CREATE TABLE ingredient_amount (ingredient_amount_id INT NOT NULL, ingredient_id INT DEFAULT NULL, recipe_id INT DEFAULT NULL, ingredient_amount_type_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(ingredient_amount_id))');
        $this->addSql('CREATE INDEX IDX_62DBB003933FE08C ON ingredient_amount (ingredient_id)');
        $this->addSql('CREATE INDEX IDX_62DBB00359D8A214 ON ingredient_amount (recipe_id)');
        $this->addSql('CREATE INDEX IDX_62DBB00384F5ED6C ON ingredient_amount (ingredient_amount_type_id)');
        $this->addSql('CREATE TABLE ingredient (ingredient_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(ingredient_id))');
        $this->addSql('ALTER TABLE user_information ADD CONSTRAINT fk_users_id FOREIGN KEY (user_id) REFERENCES "user" (user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT fk_users_information_id FOREIGN KEY (user_information_id) REFERENCES user_information (user_information_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient_amount ADD CONSTRAINT fk_ingredients FOREIGN KEY (ingredient_id) REFERENCES ingredient (ingredient_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient_amount ADD CONSTRAINT fk_recipe FOREIGN KEY (recipe_id) REFERENCES recipe (recipe_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient_amount ADD CONSTRAINT fk_ingredient_amount_type FOREIGN KEY (ingredient_amount_type_id) REFERENCES ingredient_amount_type (ingredient_amount_type_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP INDEX users_pkey');
        $this->addSql('ALTER TABLE "user" ALTER name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "user" ALTER role SET DEFAULT \'user\'');
        $this->addSql('ALTER TABLE "user" ALTER role DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN id TO user_id');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (user_id)');
    }
}
