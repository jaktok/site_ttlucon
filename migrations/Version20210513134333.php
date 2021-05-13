<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210513134333 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE joueurs DROP FOREIGN KEY FK_F0FD889DD60322AC');
        $this->addSql('DROP TABLE categories_joueurs');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP INDEX IDX_F0FD889DD60322AC ON joueurs');
        $this->addSql('ALTER TABLE joueurs CHANGE role_id categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE joueurs ADD CONSTRAINT FK_F0FD889DA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_F0FD889DA21214B7 ON joueurs (categories_id)');
        $this->addSql('ALTER TABLE rencontres ADD equipe_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rencontres ADD CONSTRAINT FK_C5F35DFB1D4C902F FOREIGN KEY (equipe_type_id) REFERENCES equipe_type (id)');
        $this->addSql('CREATE INDEX IDX_C5F35DFB1D4C902F ON rencontres (equipe_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories_joueurs (categories_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_D8F7593DA21214B7 (categories_id), INDEX IDX_D8F7593DA3DC7281 (joueurs_id), PRIMARY KEY(categories_id, joueurs_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE categories_joueurs ADD CONSTRAINT FK_D8F7593DA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories_joueurs ADD CONSTRAINT FK_D8F7593DA3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE joueurs DROP FOREIGN KEY FK_F0FD889DA21214B7');
        $this->addSql('DROP INDEX IDX_F0FD889DA21214B7 ON joueurs');
        $this->addSql('ALTER TABLE joueurs CHANGE categories_id role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE joueurs ADD CONSTRAINT FK_F0FD889DD60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('CREATE INDEX IDX_F0FD889DD60322AC ON joueurs (role_id)');
        $this->addSql('ALTER TABLE rencontres DROP FOREIGN KEY FK_C5F35DFB1D4C902F');
        $this->addSql('DROP INDEX IDX_C5F35DFB1D4C902F ON rencontres');
        $this->addSql('ALTER TABLE rencontres DROP equipe_type_id');
    }
}
