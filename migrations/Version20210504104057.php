<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504104057 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, auteur VARCHAR(100) NOT NULL, text LONGTEXT DEFAULT NULL, date DATETIME DEFAULT NULL, en_ligne TINYINT(1) DEFAULT NULL, INDEX IDX_BFDD3168A9E2D76C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories_joueurs (categories_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_D8F7593DA21214B7 (categories_id), INDEX IDX_D8F7593DA3DC7281 (joueurs_id), PRIMARY KEY(categories_id, joueurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classement (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, points INT NOT NULL, date DATETIME DEFAULT NULL, INDEX IDX_55EE9D6DA9E2D76C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, type_competition_id INT NOT NULL, nom VARCHAR(100) NOT NULL, num_journee INT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_B50A2CB12DFAFA86 (type_competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_joueurs (competition_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_EF4848337B39D312 (competition_id), INDEX IDX_EF484833A3DC7281 (joueurs_id), PRIMARY KEY(competition_id, joueurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entrainement (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, libelle VARCHAR(100) NOT NULL, jour VARCHAR(50) DEFAULT NULL, heure_debut DATETIME DEFAULT NULL, heure_fin DATETIME DEFAULT NULL, INDEX IDX_A27444E5BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_rencontre (id INT AUTO_INCREMENT NOT NULL, fichier_id INT DEFAULT NULL, categories_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, capitaine INT DEFAULT NULL, saison VARCHAR(20) DEFAULT NULL, UNIQUE INDEX UNIQ_1A7D5016F915CFE (fichier_id), INDEX IDX_1A7D5016A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_rencontre_joueurs (equipe_rencontre_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_805829C7EAE54A3D (equipe_rencontre_id), INDEX IDX_805829C7A3DC7281 (joueurs_id), PRIMARY KEY(equipe_rencontre_id, joueurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_type_joueurs (equipe_type_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_5EC5A54D1D4C902F (equipe_type_id), INDEX IDX_5EC5A54DA3DC7281 (joueurs_id), PRIMARY KEY(equipe_type_id, joueurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichiers (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, equipe_type_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, url VARCHAR(150) NOT NULL, format VARCHAR(10) DEFAULT NULL, UNIQUE INDEX UNIQ_969DB4ABA9E2D76C (joueur_id), UNIQUE INDEX UNIQ_969DB4AB1D4C902F (equipe_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE infos_club (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, contenu LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueurs (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, mail VARCHAR(50) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, cp VARCHAR(5) DEFAULT NULL, ville VARCHAR(50) DEFAULT NULL, certificat TINYINT(1) DEFAULT NULL, cotisation TINYINT(1) DEFAULT NULL, divers LONGTEXT DEFAULT NULL, bureau TINYINT(1) NOT NULL, num_licence VARCHAR(20) DEFAULT NULL, date_naissance DATETIME DEFAULT NULL, nom_photo VARCHAR(20) DEFAULT NULL, date_certificat DATETIME DEFAULT NULL, indiv TINYINT(1) NOT NULL, contact_nom VARCHAR(50) DEFAULT NULL, contact_prenom VARCHAR(50) DEFAULT NULL, contact_tel VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matchs (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, competition_id INT DEFAULT NULL, rencontre_id INT DEFAULT NULL, victoire TINYINT(1) DEFAULT NULL, set_gagne INT DEFAULT NULL, set_perdu INT DEFAULT NULL, INDEX IDX_6B1E6041A9E2D76C (joueur_id), INDEX IDX_6B1E60417B39D312 (competition_id), INDEX IDX_6B1E60416CFC0818 (rencontre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rencontres (id INT AUTO_INCREMENT NOT NULL, equipe_rencontre_id INT DEFAULT NULL, date_rencontre DATETIME NOT NULL, adversaire VARCHAR(100) NOT NULL, domicile TINYINT(1) NOT NULL, no_journee INT NOT NULL, victoire TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_C5F35DFBEAE54A3D (equipe_rencontre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarif (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, prix INT DEFAULT NULL, saison VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_competition (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE categories_joueurs ADD CONSTRAINT FK_D8F7593DA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories_joueurs ADD CONSTRAINT FK_D8F7593DA3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classement ADD CONSTRAINT FK_55EE9D6DA9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB12DFAFA86 FOREIGN KEY (type_competition_id) REFERENCES type_competition (id)');
        $this->addSql('ALTER TABLE competition_joueurs ADD CONSTRAINT FK_EF4848337B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_joueurs ADD CONSTRAINT FK_EF484833A3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entrainement ADD CONSTRAINT FK_A27444E5BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE equipe_rencontre ADD CONSTRAINT FK_1A7D5016F915CFE FOREIGN KEY (fichier_id) REFERENCES fichiers (id)');
        $this->addSql('ALTER TABLE equipe_rencontre ADD CONSTRAINT FK_1A7D5016A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs ADD CONSTRAINT FK_805829C7EAE54A3D FOREIGN KEY (equipe_rencontre_id) REFERENCES equipe_rencontre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs ADD CONSTRAINT FK_805829C7A3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_type_joueurs ADD CONSTRAINT FK_5EC5A54D1D4C902F FOREIGN KEY (equipe_type_id) REFERENCES equipe_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_type_joueurs ADD CONSTRAINT FK_5EC5A54DA3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT FK_969DB4ABA9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT FK_969DB4AB1D4C902F FOREIGN KEY (equipe_type_id) REFERENCES equipe_type (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E6041A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60417B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60416CFC0818 FOREIGN KEY (rencontre_id) REFERENCES rencontres (id)');
        $this->addSql('ALTER TABLE rencontres ADD CONSTRAINT FK_C5F35DFBEAE54A3D FOREIGN KEY (equipe_rencontre_id) REFERENCES equipe_rencontre (id)');
        $this->addSql('DROP TABLE joueur');
        $this->addSql('ALTER TABLE equipe_type ADD categories_id INT DEFAULT NULL, CHANGE nom nom VARCHAR(20) NOT NULL, CHANGE division division VARCHAR(20) NOT NULL, CHANGE saison saison VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE equipe_type ADD CONSTRAINT FK_8597C6D7A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_8597C6D7A21214B7 ON equipe_type (categories_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories_joueurs DROP FOREIGN KEY FK_D8F7593DA21214B7');
        $this->addSql('ALTER TABLE entrainement DROP FOREIGN KEY FK_A27444E5BCF5E72D');
        $this->addSql('ALTER TABLE equipe_rencontre DROP FOREIGN KEY FK_1A7D5016A21214B7');
        $this->addSql('ALTER TABLE equipe_type DROP FOREIGN KEY FK_8597C6D7A21214B7');
        $this->addSql('ALTER TABLE competition_joueurs DROP FOREIGN KEY FK_EF4848337B39D312');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60417B39D312');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs DROP FOREIGN KEY FK_805829C7EAE54A3D');
        $this->addSql('ALTER TABLE rencontres DROP FOREIGN KEY FK_C5F35DFBEAE54A3D');
        $this->addSql('ALTER TABLE equipe_rencontre DROP FOREIGN KEY FK_1A7D5016F915CFE');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A9E2D76C');
        $this->addSql('ALTER TABLE categories_joueurs DROP FOREIGN KEY FK_D8F7593DA3DC7281');
        $this->addSql('ALTER TABLE classement DROP FOREIGN KEY FK_55EE9D6DA9E2D76C');
        $this->addSql('ALTER TABLE competition_joueurs DROP FOREIGN KEY FK_EF484833A3DC7281');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs DROP FOREIGN KEY FK_805829C7A3DC7281');
        $this->addSql('ALTER TABLE equipe_type_joueurs DROP FOREIGN KEY FK_5EC5A54DA3DC7281');
        $this->addSql('ALTER TABLE fichiers DROP FOREIGN KEY FK_969DB4ABA9E2D76C');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E6041A9E2D76C');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60416CFC0818');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB12DFAFA86');
        $this->addSql('CREATE TABLE joueur (id INT AUTO_INCREMENT NOT NULL, id_equipe_type_id INT DEFAULT NULL, equipe_type_id INT DEFAULT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, mail VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, telephone VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, adresse VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, cp VARCHAR(5) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ville VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, certificat TINYINT(1) DEFAULT NULL, cotisation TINYINT(1) DEFAULT NULL, divers VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, bureau TINYINT(1) DEFAULT NULL, licence INT DEFAULT NULL, date_naissance DATETIME DEFAULT NULL, date_certificat DATETIME DEFAULT NULL, nom_photo VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, indiv TINYINT(1) DEFAULT NULL, contact_nom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, contact_prenom VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, contact_tel VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_FD71A9C5202A2D29 (id_equipe_type_id), INDEX IDX_FD71A9C51D4C902F (equipe_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C51D4C902F FOREIGN KEY (equipe_type_id) REFERENCES equipe_type (id)');
        $this->addSql('ALTER TABLE joueur ADD CONSTRAINT FK_FD71A9C5202A2D29 FOREIGN KEY (id_equipe_type_id) REFERENCES equipe_type (id)');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE categories_joueurs');
        $this->addSql('DROP TABLE classement');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_joueurs');
        $this->addSql('DROP TABLE entrainement');
        $this->addSql('DROP TABLE equipe_rencontre');
        $this->addSql('DROP TABLE equipe_rencontre_joueurs');
        $this->addSql('DROP TABLE equipe_type_joueurs');
        $this->addSql('DROP TABLE fichiers');
        $this->addSql('DROP TABLE infos_club');
        $this->addSql('DROP TABLE joueurs');
        $this->addSql('DROP TABLE matchs');
        $this->addSql('DROP TABLE rencontres');
        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE type_competition');
        $this->addSql('DROP INDEX IDX_8597C6D7A21214B7 ON equipe_type');
        $this->addSql('ALTER TABLE equipe_type DROP categories_id, CHANGE nom nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE division division INT NOT NULL, CHANGE saison saison VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
