SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `db-hauleben` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `db-hauleben`;

DROP TABLE IF EXISTS `competence`;
CREATE TABLE `competence` (
  `id_competence` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  PRIMARY KEY (`id_competence`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id_utilisateur` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_contact`),
  KEY `id_contact` (`id_contact`),
  CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `evaluation`;
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


DROP TABLE IF EXISTS `favoris_mission`;
CREATE TABLE `favoris_mission` (
  `id_utilisateur` int(11) NOT NULL,
  `id_mission` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_utilisateur`,`id_mission`),
  KEY `id_mission` (`id_mission`),
  CONSTRAINT `favoris_mission_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `favoris_mission_ibfk_2` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `favoris_organisation`;
CREATE TABLE `favoris_organisation` (
  `id_utilisateur` int(11) NOT NULL,
  `id_organisation` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_utilisateur`,`id_organisation`),
  KEY `id_organisation` (`id_organisation`),
  CONSTRAINT `favoris_organisation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `favoris_organisation_ibfk_2` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `inscription`;
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


DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id_message` int(11) NOT NULL AUTO_INCREMENT,
  `id_envoyeur` int(11) NOT NULL,
  `id_destinataire` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_message`),
  KEY `id_envoyeur` (`id_envoyeur`),
  KEY `id_destinataire` (`id_destinataire`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_envoyeur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`id_destinataire`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Créer une nouvelle table conversation
CREATE TABLE `conversation` (
  `id_conversation` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_conversation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Créer une table pour lier les utilisateurs aux conversations
CREATE TABLE `conversation_participant` (
  `id_conversation` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  PRIMARY KEY (`id_conversation`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `conversation_participant_ibfk_1` FOREIGN KEY (`id_conversation`) REFERENCES `conversation` (`id_conversation`) ON DELETE CASCADE,
  CONSTRAINT `conversation_participant_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Modifier la table message pour utiliser id_conversation au lieu de id_destinataire
ALTER TABLE `message` DROP FOREIGN KEY `message_ibfk_2`;
ALTER TABLE `message` DROP COLUMN `id_destinataire`;
ALTER TABLE `message` ADD COLUMN `id_conversation` int(11) NOT NULL AFTER `id_envoyeur`;
ALTER TABLE `message` ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`id_conversation`) REFERENCES `conversation` (`id_conversation`) ON DELETE CASCADE;

DROP TABLE IF EXISTS `mission`;
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


DROP TABLE IF EXISTS `mission_competence`;
CREATE TABLE `mission_competence` (
  `id_mission` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL,
  PRIMARY KEY (`id_mission`,`id_competence`),
  KEY `id_competence` (`id_competence`),
  CONSTRAINT `mission_competence_ibfk_1` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE,
  CONSTRAINT `mission_competence_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competence` (`id_competence`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `organisation`;
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
  `description_organisation` text DEFAULT NULL,
  PRIMARY KEY (`id_organisation`),
  UNIQUE KEY `email_contact` (`email_contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `organisation_representant`;
CREATE TABLE `organisation_representant` (
  `id_organisation` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  PRIMARY KEY (`id_organisation`,`id_utilisateur`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `organisation_representant_ibfk_1` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE,
  CONSTRAINT `organisation_representant_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `identifiant` tinyint(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('1','2','3') NOT NULL DEFAULT '1',
  `tagUsers` text DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` text NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT current_timestamp(),
  `handicap` tinyint(1) DEFAULT 0,
  `description_handicap` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `utilisateur_competence`;
CREATE TABLE `utilisateur_competence` (
  `id_utilisateur` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_competence`),
  KEY `id_competence` (`id_competence`),
  CONSTRAINT `utilisateur_competence_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  CONSTRAINT `utilisateur_competence_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competence` (`id_competence`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

