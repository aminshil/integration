<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306193113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, typeofcat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, publication_id INT DEFAULT NULL, demande_id INT DEFAULT NULL, comment LONGTEXT NOT NULL, dateofcom DATETIME NOT NULL, INDEX IDX_9474526C38B217A7 (publication_id), INDEX IDX_9474526C80E95E18 (demande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, evenement_id INT NOT NULL, commentaireuser_id INT DEFAULT NULL, contenu LONGTEXT NOT NULL, datecreation DATETIME NOT NULL, INDEX IDX_67F068BCFD02F13 (evenement_id), INDEX IDX_67F068BC4AD9E491 (commentaireuser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaireformation (id INT AUTO_INCREMENT NOT NULL, commentaireuserformation_id INT DEFAULT NULL, formation_id INT NOT NULL, contenu VARCHAR(255) NOT NULL, datecreation DATETIME NOT NULL, INDEX IDX_9C91AC03384A215F (commentaireuserformation_id), INDEX IDX_9C91AC035200282E (formation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, demandeuser_id INT NOT NULL, demande LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, nameofobj VARCHAR(255) NOT NULL, stateofdem VARCHAR(255) NOT NULL, dateofdem DATE NOT NULL, INDEX IDX_2694D7A512469DE2 (category_id), INDEX IDX_2694D7A51C1972F9 (demandeuser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, user_evenement_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, objectif LONGTEXT NOT NULL, formation TINYINT(1) NOT NULL, image VARCHAR(600) NOT NULL, location VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, etat VARCHAR(255) NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, locationtext VARCHAR(255) NOT NULL, INDEX IDX_B26681E8F1C5311 (user_evenement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement_user (evenement_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2EC0B3C4FD02F13 (evenement_id), INDEX IDX_2EC0B3C4A76ED395 (user_id), PRIMARY KEY(evenement_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, formationuser_id INT NOT NULL, nom_formation VARCHAR(255) NOT NULL, description_formation VARCHAR(800) NOT NULL, date_formation DATE NOT NULL, formateur VARCHAR(255) NOT NULL, lieu_formation VARCHAR(255) NOT NULL, image_formation VARCHAR(255) NOT NULL, INDEX IDX_404021BF7E443A3B (formationuser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_user (formation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_DA4C33095200282E (formation_id), INDEX IDX_DA4C3309A76ED395 (user_id), PRIMARY KEY(formation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, publicationuser_id INT NOT NULL, publication LONGTEXT NOT NULL, dateofpub DATE NOT NULL, topicofpub VARCHAR(255) NOT NULL, imagepub VARCHAR(255) DEFAULT NULL, INDEX IDX_AF3C6779BAB6255B (publicationuser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, date_de_naissance DATE NOT NULL, is_verified TINYINT(1) NOT NULL, is_blocked VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C80E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC4AD9E491 FOREIGN KEY (commentaireuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commentaireformation ADD CONSTRAINT FK_9C91AC03384A215F FOREIGN KEY (commentaireuserformation_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commentaireformation ADD CONSTRAINT FK_9C91AC035200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A51C1972F9 FOREIGN KEY (demandeuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E8F1C5311 FOREIGN KEY (user_evenement_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE evenement_user ADD CONSTRAINT FK_2EC0B3C4FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE evenement_user ADD CONSTRAINT FK_2EC0B3C4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF7E443A3B FOREIGN KEY (formationuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE formation_user ADD CONSTRAINT FK_DA4C33095200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_user ADD CONSTRAINT FK_DA4C3309A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779BAB6255B FOREIGN KEY (publicationuser_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C38B217A7');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C80E95E18');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCFD02F13');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC4AD9E491');
        $this->addSql('ALTER TABLE commentaireformation DROP FOREIGN KEY FK_9C91AC03384A215F');
        $this->addSql('ALTER TABLE commentaireformation DROP FOREIGN KEY FK_9C91AC035200282E');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A512469DE2');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A51C1972F9');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E8F1C5311');
        $this->addSql('ALTER TABLE evenement_user DROP FOREIGN KEY FK_2EC0B3C4FD02F13');
        $this->addSql('ALTER TABLE evenement_user DROP FOREIGN KEY FK_2EC0B3C4A76ED395');
        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BF7E443A3B');
        $this->addSql('ALTER TABLE formation_user DROP FOREIGN KEY FK_DA4C33095200282E');
        $this->addSql('ALTER TABLE formation_user DROP FOREIGN KEY FK_DA4C3309A76ED395');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779BAB6255B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE commentaireformation');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_user');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_user');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
