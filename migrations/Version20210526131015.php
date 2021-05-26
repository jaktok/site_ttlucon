<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210526131015 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE articles (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, auteur VARCHAR(100) DEFAULT NULL, text LONGTEXT DEFAULT NULL, date DATETIME DEFAULT NULL, en_ligne TINYINT(1) DEFAULT NULL, titre VARCHAR(255) NOT NULL, INDEX IDX_BFDD3168A9E2D76C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classement (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, points INT NOT NULL, date DATETIME DEFAULT NULL, INDEX IDX_55EE9D6DA9E2D76C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, categories_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, num_journee INT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_B50A2CB1A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_joueurs (competition_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_EF4848337B39D312 (competition_id), INDEX IDX_EF484833A3DC7281 (joueurs_id), PRIMARY KEY(competition_id, joueurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE doc_accueil (id INT AUTO_INCREMENT NOT NULL, fichier_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, actif TINYINT(1) NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_DD7BC460F915CFE (fichier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entrainement (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, libelle VARCHAR(100) NOT NULL, jour VARCHAR(50) DEFAULT NULL, heure_debut TIME DEFAULT NULL, heure_fin TIME DEFAULT NULL, INDEX IDX_A27444E5BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_rencontre (id INT AUTO_INCREMENT NOT NULL, fichier_id INT DEFAULT NULL, categories_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, capitaine INT DEFAULT NULL, saison VARCHAR(20) DEFAULT NULL, numero INT DEFAULT NULL, UNIQUE INDEX UNIQ_1A7D5016F915CFE (fichier_id), INDEX IDX_1A7D5016A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_rencontre_joueurs (equipe_rencontre_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_805829C7EAE54A3D (equipe_rencontre_id), INDEX IDX_805829C7A3DC7281 (joueurs_id), PRIMARY KEY(equipe_rencontre_id, joueurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_type (id INT AUTO_INCREMENT NOT NULL, categories_id INT DEFAULT NULL, nom VARCHAR(20) NOT NULL, num_equipe INT NOT NULL, division VARCHAR(20) NOT NULL, saison VARCHAR(10) DEFAULT NULL, capitaine VARCHAR(255) DEFAULT NULL, salle VARCHAR(255) DEFAULT NULL, INDEX IDX_8597C6D7A21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipe_type_joueurs (equipe_type_id INT NOT NULL, joueurs_id INT NOT NULL, INDEX IDX_5EC5A54D1D4C902F (equipe_type_id), INDEX IDX_5EC5A54DA3DC7281 (joueurs_id), PRIMARY KEY(equipe_type_id, joueurs_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichiers (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, equipe_type_id INT DEFAULT NULL, articles_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, url VARCHAR(150) NOT NULL, format VARCHAR(10) DEFAULT NULL, UNIQUE INDEX UNIQ_969DB4ABA9E2D76C (joueur_id), UNIQUE INDEX UNIQ_969DB4AB1D4C902F (equipe_type_id), INDEX IDX_969DB4AB1EBAF6CC (articles_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fonction (id INT AUTO_INCREMENT NOT NULL, joueurs_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, position INT NOT NULL, UNIQUE INDEX UNIQ_900D5BDA3DC7281 (joueurs_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE infos_club (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, contenu LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE joueurs (id INT AUTO_INCREMENT NOT NULL, categories_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, mail VARCHAR(50) DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, cp VARCHAR(5) DEFAULT NULL, ville VARCHAR(50) DEFAULT NULL, certificat TINYINT(1) DEFAULT NULL, cotisation TINYINT(1) DEFAULT NULL, divers LONGTEXT DEFAULT NULL, bureau TINYINT(1) NOT NULL, num_licence VARCHAR(20) DEFAULT NULL, date_naissance DATETIME DEFAULT NULL, nom_photo VARCHAR(20) DEFAULT NULL, date_certificat DATETIME DEFAULT NULL, indiv TINYINT(1) NOT NULL, contact_nom VARCHAR(50) DEFAULT NULL, contact_prenom VARCHAR(50) DEFAULT NULL, contact_tel VARCHAR(20) DEFAULT NULL, INDEX IDX_F0FD889DA21214B7 (categories_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matchs (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, competition_id INT DEFAULT NULL, rencontre_id INT DEFAULT NULL, victoire TINYINT(1) DEFAULT NULL, position INT DEFAULT NULL, match_double TINYINT(1) DEFAULT NULL, score INT DEFAULT NULL, double1 VARCHAR(255) DEFAULT NULL, double2 VARCHAR(255) DEFAULT NULL, INDEX IDX_6B1E6041A9E2D76C (joueur_id), INDEX IDX_6B1E60417B39D312 (competition_id), INDEX IDX_6B1E60416CFC0818 (rencontre_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaire (id INT AUTO_INCREMENT NOT NULL, fichier_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, actif TINYINT(1) NOT NULL, texte LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_32FFA373F915CFE (fichier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rencontres (id INT AUTO_INCREMENT NOT NULL, equipe_type_id INT DEFAULT NULL, date_rencontre DATETIME NOT NULL, domicile TINYINT(1) NOT NULL, no_journee INT NOT NULL, victoire TINYINT(1) DEFAULT NULL, phase INT DEFAULT NULL, equipe_a VARCHAR(255) DEFAULT NULL, equipe_b VARCHAR(255) DEFAULT NULL, score_a INT DEFAULT NULL, score_b INT DEFAULT NULL, INDEX IDX_C5F35DFB1D4C902F (equipe_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarif (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, prix INT DEFAULT NULL, saison VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_competition (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, joueur_id INT DEFAULT NULL, login VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), UNIQUE INDEX UNIQ_1483A5E9A9E2D76C (joueur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE classement ADD CONSTRAINT FK_55EE9D6DA9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE competition_joueurs ADD CONSTRAINT FK_EF4848337B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_joueurs ADD CONSTRAINT FK_EF484833A3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE doc_accueil ADD CONSTRAINT FK_DD7BC460F915CFE FOREIGN KEY (fichier_id) REFERENCES fichiers (id)');
        $this->addSql('ALTER TABLE entrainement ADD CONSTRAINT FK_A27444E5BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE equipe_rencontre ADD CONSTRAINT FK_1A7D5016F915CFE FOREIGN KEY (fichier_id) REFERENCES fichiers (id)');
        $this->addSql('ALTER TABLE equipe_rencontre ADD CONSTRAINT FK_1A7D5016A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs ADD CONSTRAINT FK_805829C7EAE54A3D FOREIGN KEY (equipe_rencontre_id) REFERENCES equipe_rencontre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs ADD CONSTRAINT FK_805829C7A3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_type ADD CONSTRAINT FK_8597C6D7A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE equipe_type_joueurs ADD CONSTRAINT FK_5EC5A54D1D4C902F FOREIGN KEY (equipe_type_id) REFERENCES equipe_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE equipe_type_joueurs ADD CONSTRAINT FK_5EC5A54DA3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT FK_969DB4ABA9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT FK_969DB4AB1D4C902F FOREIGN KEY (equipe_type_id) REFERENCES equipe_type (id)');
        $this->addSql('ALTER TABLE fichiers ADD CONSTRAINT FK_969DB4AB1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE fonction ADD CONSTRAINT FK_900D5BDA3DC7281 FOREIGN KEY (joueurs_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE joueurs ADD CONSTRAINT FK_F0FD889DA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E6041A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60417B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE matchs ADD CONSTRAINT FK_6B1E60416CFC0818 FOREIGN KEY (rencontre_id) REFERENCES rencontres (id)');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA373F915CFE FOREIGN KEY (fichier_id) REFERENCES fichiers (id)');
        $this->addSql('ALTER TABLE rencontres ADD CONSTRAINT FK_C5F35DFB1D4C902F FOREIGN KEY (equipe_type_id) REFERENCES equipe_type (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9A9E2D76C FOREIGN KEY (joueur_id) REFERENCES joueurs (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fichiers DROP FOREIGN KEY FK_969DB4AB1EBAF6CC');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1A21214B7');
        $this->addSql('ALTER TABLE entrainement DROP FOREIGN KEY FK_A27444E5BCF5E72D');
        $this->addSql('ALTER TABLE equipe_rencontre DROP FOREIGN KEY FK_1A7D5016A21214B7');
        $this->addSql('ALTER TABLE equipe_type DROP FOREIGN KEY FK_8597C6D7A21214B7');
        $this->addSql('ALTER TABLE joueurs DROP FOREIGN KEY FK_F0FD889DA21214B7');
        $this->addSql('ALTER TABLE competition_joueurs DROP FOREIGN KEY FK_EF4848337B39D312');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60417B39D312');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs DROP FOREIGN KEY FK_805829C7EAE54A3D');
        $this->addSql('ALTER TABLE equipe_type_joueurs DROP FOREIGN KEY FK_5EC5A54D1D4C902F');
        $this->addSql('ALTER TABLE fichiers DROP FOREIGN KEY FK_969DB4AB1D4C902F');
        $this->addSql('ALTER TABLE rencontres DROP FOREIGN KEY FK_C5F35DFB1D4C902F');
        $this->addSql('ALTER TABLE doc_accueil DROP FOREIGN KEY FK_DD7BC460F915CFE');
        $this->addSql('ALTER TABLE equipe_rencontre DROP FOREIGN KEY FK_1A7D5016F915CFE');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373F915CFE');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A9E2D76C');
        $this->addSql('ALTER TABLE classement DROP FOREIGN KEY FK_55EE9D6DA9E2D76C');
        $this->addSql('ALTER TABLE competition_joueurs DROP FOREIGN KEY FK_EF484833A3DC7281');
        $this->addSql('ALTER TABLE equipe_rencontre_joueurs DROP FOREIGN KEY FK_805829C7A3DC7281');
        $this->addSql('ALTER TABLE equipe_type_joueurs DROP FOREIGN KEY FK_5EC5A54DA3DC7281');
        $this->addSql('ALTER TABLE fichiers DROP FOREIGN KEY FK_969DB4ABA9E2D76C');
        $this->addSql('ALTER TABLE fonction DROP FOREIGN KEY FK_900D5BDA3DC7281');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E6041A9E2D76C');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9A9E2D76C');
        $this->addSql('ALTER TABLE matchs DROP FOREIGN KEY FK_6B1E60416CFC0818');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE classement');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_joueurs');
        $this->addSql('DROP TABLE doc_accueil');
        $this->addSql('DROP TABLE entrainement');
        $this->addSql('DROP TABLE equipe_rencontre');
        $this->addSql('DROP TABLE equipe_rencontre_joueurs');
        $this->addSql('DROP TABLE equipe_type');
        $this->addSql('DROP TABLE equipe_type_joueurs');
        $this->addSql('DROP TABLE fichiers');
        $this->addSql('DROP TABLE fonction');
        $this->addSql('DROP TABLE infos_club');
        $this->addSql('DROP TABLE joueurs');
        $this->addSql('DROP TABLE matchs');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE rencontres');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE tarif');
        $this->addSql('DROP TABLE type_competition');
        $this->addSql('DROP TABLE users');
    }
}
