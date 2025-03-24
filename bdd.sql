-- Adminer 4.8.1 MySQL 10.6.18-MariaDB-0ubuntu0.22.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

<<<<<<< HEAD
SET NAMES utf8mb4;  
=======
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `db-hauleben` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `db-hauleben`;
>>>>>>> c2c38e0c43e0683995e89f12fee7f0e78077b5d4

-- Suppression de toutes les tables dans l'ordre inverse des dépendances
DROP TABLE IF EXISTS `utilisateur_competence`;
DROP TABLE IF EXISTS `organisation_representant`;
DROP TABLE IF EXISTS `mission_competence`;
DROP TABLE IF EXISTS `favoris_organisation`;
DROP TABLE IF EXISTS `favoris_mission`;
DROP TABLE IF EXISTS `inscription`;
DROP TABLE IF EXISTS `evaluation`;
DROP TABLE IF EXISTS `message`;
DROP TABLE IF EXISTS `conversation_participant`;
DROP TABLE IF EXISTS `mission`;
DROP TABLE IF EXISTS `contact`;
DROP TABLE IF EXISTS `conversation`;
DROP TABLE IF EXISTS `organisation`;
DROP TABLE IF EXISTS `competence`;
DROP TABLE IF EXISTS `utilisateur`;

-- Création des tables sans dépendances en premier
CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `identifiant` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('1','2','3') NOT NULL DEFAULT '1',
  `tagUsers` text DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` text NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `handicap` tinyint(1) DEFAULT 0,
  `description_handicap` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `competence` (
  `id_competence` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id_competence`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `organisation` (
  `id_organisation` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `email_contact` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `statut` enum('en_attente','approuvé','rejeté') DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_organisation`),
  UNIQUE KEY `email_contact` (`email_contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `conversation` (
  `id_conversation` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_conversation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tables avec dépendances simples
CREATE TABLE `contact` (
  `id_utilisateur` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_contact`),
  KEY `id_contact` (`id_contact`),
  CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `mission` (
  `id_mission` int(11) NOT NULL AUTO_INCREMENT,
  `id_organisation` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `nb_places` int(11) NOT NULL,
  `statut` enum('ouverte','fermée','en attente') DEFAULT 'ouverte',
  `date_creation` datetime DEFAULT current_timestamp(),
  `handicap` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_mission`),
  KEY `id_organisation` (`id_organisation`),
  CONSTRAINT `mission_ibfk_1` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `conversation_participant` (
  `id_conversation` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  PRIMARY KEY (`id_conversation`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `conversation_participant_ibfk_1` FOREIGN KEY (`id_conversation`) REFERENCES `conversation` (`id_conversation`) ON DELETE CASCADE,
  CONSTRAINT `conversation_participant_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tables avec dépendances multiples
CREATE TABLE `message` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `id_envoyeur` int(11) NOT NULL,
  `id_conversation` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_message`),
  KEY `id_envoyeur` (`id_envoyeur`),
  KEY `message_ibfk_2` (`id_conversation`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_envoyeur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`id_conversation`) REFERENCES `conversation` (`id_conversation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `evaluation` (
  `id_evaluation` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_organisation` int(11) DEFAULT NULL,
  `id_mission` int(11) NOT NULL,
  `note` int(11) DEFAULT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `date_evaluation` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_evaluation`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_organisation` (`id_organisation`),
  KEY `id_mission` (`id_mission`),
  CONSTRAINT `evaluation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL,
  CONSTRAINT `evaluation_ibfk_2` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE SET NULL,
  CONSTRAINT `evaluation_ibfk_3` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `inscription` (
  `id_inscription` int(11) NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int(11) NOT NULL,
  `id_mission` int(11) NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `statut` enum('validée','en attente','annulée') DEFAULT 'en attente',
  PRIMARY KEY (`id_inscription`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_mission` (`id_mission`),
  CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `favoris_mission` (
  `id_utilisateur` int(11) NOT NULL,
  `id_mission` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_utilisateur`,`id_mission`),
  KEY `id_mission` (`id_mission`),
  CONSTRAINT `favoris_mission_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `favoris_mission_ibfk_2` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `favoris_organisation` (
  `id_utilisateur` int(11) NOT NULL,
  `id_organisation` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_utilisateur`,`id_organisation`),
  KEY `id_organisation` (`id_organisation`),
  CONSTRAINT `favoris_organisation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `favoris_organisation_ibfk_2` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `mission_competence` (
  `id_mission` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL,
  PRIMARY KEY (`id_mission`,`id_competence`),
  KEY `id_competence` (`id_competence`),
  CONSTRAINT `mission_competence_ibfk_1` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE,
  CONSTRAINT `mission_competence_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competence` (`id_competence`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `organisation_representant` (
  `id_organisation` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  PRIMARY KEY (`id_organisation`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `organisation_representant_ibfk_1` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE,
  CONSTRAINT `organisation_representant_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `utilisateur_competence` (
  `id_utilisateur` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_competence`),
  KEY `id_competence` (`id_competence`),
  CONSTRAINT `utilisateur_competence_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `utilisateur_competence_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competence` (`id_competence`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertion des données dans l'ordre des dépendances
INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `identifiant`, `mot_de_passe`, `role`, `tagUsers`, `telephone`, `adresse`, `ville`, `code_postal`, `date_inscription`, `handicap`, `description_handicap`) VALUES
(1, 'Haulet', 'Benjamin', 'benjamin.haulet@univ-rouen.fr', 'bhaulet', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '1', 'lecture', '0634480212', '45 rue Henry ', 'Elbeuf', '76500', '2025-03-17 10:28:11', 0, NULL),
(2, 'Leborgne', 'Justine', 'leborgne.justine8129@gmail.com', 'leborjus', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '1', 'Voyage', '0612345678', '234 route de la mairie', 'Rouen', '76690', '2025-03-20 15:45:29', 0, NULL),
(3, 'Duclos', 'Corentin', 'duclos_corentin@gmail.com', 'Cduclos', 'da39a3ee5e6b4b0d3255bfef95601890afd80709', '1', 'sport', '0612345678', '11 rue de la Paix', 'Elbeuf', '76500', '2025-03-24 08:40:43', 0, NULL),
(4, 'admin', 'admin', 'admin@admin.com', 'admin', '7b902e6ff1db9f560443f2048974fd7d386975b0', '3', NULL, '', '', '', '', '2025-03-24 10:47:40', 0, NULL);

INSERT INTO `competence` (`id_competence`, `nom`) VALUES
(1, 'Communication');

INSERT INTO `organisation` (`id_organisation`, `nom`, `description`, `email_contact`, `telephone`, `adresse`, `ville`, `code_postal`, `pays`, `site_web`, `date_creation`, `statut`, `logo`) VALUES
(1, 'Orga_test', 'Test orga', 'orga_test@gmail.com', '0600000000', '11 rue de la Paix', 'Elbeuf', '76000', 'France', 'https://organisation.com', '2025-03-20 11:07:28', 'approuvé', 'restoCoeur.png');

INSERT INTO `conversation` (`id_conversation`, `titre`, `date_creation`) VALUES
(1, 'Conversation: Benjamin Haulet & Justine Leborgne', '2025-03-24 09:18:04'),
(2, 'Conversation: Benjamin Haulet & Corentin Duclos', '2025-03-24 09:18:11');

INSERT INTO `contact` (`id_utilisateur`, `id_contact`) VALUES
(1, 2),
(1, 3),
(2, 1),
(3, 1);

INSERT INTO `conversation_participant` (`id_conversation`, `id_utilisateur`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3);

INSERT INTO `message` (`id_message`, `id_envoyeur`, `id_conversation`, `contenu`, `date_envoi`) VALUES
(1, 1, 2, 'Salut Corentin', '2025-03-24 09:27:02'),
(2, 1, 1, 'Coucou Justine', '2025-03-24 09:27:34');

INSERT INTO `utilisateur_competence` (`id_utilisateur`, `id_competence`) VALUES
(2, 1);

-- Rétablir les contrôles de clés étrangères
SET foreign_key_checks = 1;