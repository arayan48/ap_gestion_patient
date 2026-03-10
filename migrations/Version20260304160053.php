<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260304160053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, numero_chambre VARCHAR(20) NOT NULL, type_chambre VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, description LONGTEXT DEFAULT NULL, etage_id INT NOT NULL, INDEX IDX_C509E4FF984CE93F (etage_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE employe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, date_embauche DATE NOT NULL, statut VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_F804D3B9E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE employe_role (employe_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_F816450A1B65292 (employe_id), INDEX IDX_F816450AD60322AC (role_id), PRIMARY KEY (employe_id, role_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE etage (id INT AUTO_INCREMENT NOT NULL, numero_etage INT NOT NULL, nom_etage VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE lit (id INT AUTO_INCREMENT NOT NULL, numero_lit VARCHAR(20) NOT NULL, statut VARCHAR(20) NOT NULL, description LONGTEXT DEFAULT NULL, chambre_id INT NOT NULL, INDEX IDX_5DDB8E9D9B177F54 (chambre_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `log` (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(100) NOT NULL, table_concernee VARCHAR(100) NOT NULL, id_objet INT DEFAULT NULL, ancien_etat LONGTEXT DEFAULT NULL, nouvel_etat LONGTEXT DEFAULT NULL, date_action DATETIME NOT NULL, employe_id INT DEFAULT NULL, INDEX IDX_8F3F68C51B65292 (employe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, date_naissance DATE NOT NULL, sexe VARCHAR(10) NOT NULL, adresse LONGTEXT DEFAULT NULL, telephone VARCHAR(20) DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, numero_securite_sociale VARCHAR(15) NOT NULL, UNIQUE INDEX UNIQ_1ADAD7EB31AD32FB (numero_securite_sociale), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, statut VARCHAR(20) NOT NULL, commentaire LONGTEXT DEFAULT NULL, lit_id INT NOT NULL, patient_id INT NOT NULL, employe_id INT NOT NULL, INDEX IDX_42C84955278B5057 (lit_id), INDEX IDX_42C849556B899279 (patient_id), INDEX IDX_42C849551B65292 (employe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `role` (id INT AUTO_INCREMENT NOT NULL, nom_role VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_57698A6AA5B94004 (nom_role), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF984CE93F FOREIGN KEY (etage_id) REFERENCES etage (id)');
        $this->addSql('ALTER TABLE employe_role ADD CONSTRAINT FK_F816450A1B65292 FOREIGN KEY (employe_id) REFERENCES employe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe_role ADD CONSTRAINT FK_F816450AD60322AC FOREIGN KEY (role_id) REFERENCES `role` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lit ADD CONSTRAINT FK_5DDB8E9D9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE `log` ADD CONSTRAINT FK_8F3F68C51B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955278B5057 FOREIGN KEY (lit_id) REFERENCES lit (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849551B65292 FOREIGN KEY (employe_id) REFERENCES employe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF984CE93F');
        $this->addSql('ALTER TABLE employe_role DROP FOREIGN KEY FK_F816450A1B65292');
        $this->addSql('ALTER TABLE employe_role DROP FOREIGN KEY FK_F816450AD60322AC');
        $this->addSql('ALTER TABLE lit DROP FOREIGN KEY FK_5DDB8E9D9B177F54');
        $this->addSql('ALTER TABLE `log` DROP FOREIGN KEY FK_8F3F68C51B65292');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955278B5057');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556B899279');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849551B65292');
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE employe_role');
        $this->addSql('DROP TABLE etage');
        $this->addSql('DROP TABLE lit');
        $this->addSql('DROP TABLE `log`');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE `role`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
