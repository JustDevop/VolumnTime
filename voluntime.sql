-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 28 fév. 2025 à 08:45
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `voluntime`
--

-- --------------------------------------------------------

--
-- Structure de la table `competence`
--

CREATE TABLE `competence` (
  `id_competence` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

CREATE TABLE `evaluation` (
  `id_evaluation` int(11) NOT NULL,
  `id_utilisateur` int(11) DEFAULT NULL,
  `id_organisation` int(11) DEFAULT NULL,
  `id_mission` int(11) NOT NULL,
  `note` int(11) DEFAULT NULL CHECK (`note` between 1 and 5),
  `commentaire` text DEFAULT NULL,
  `date_evaluation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favoris_mission`
--

CREATE TABLE `favoris_mission` (
  `id_utilisateur` int(11) NOT NULL,
  `id_mission` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favoris_organisation`
--

CREATE TABLE `favoris_organisation` (
  `id_utilisateur` int(11) NOT NULL,
  `id_organisation` int(11) NOT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

CREATE TABLE `inscription` (
  `id_inscription` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `id_mission` int(11) NOT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `statut` enum('validée','en attente','annulée') DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id_message` int(11) NOT NULL,
  `id_envoyeur` int(11) NOT NULL,
  `id_destinataire` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mission`
--

CREATE TABLE `mission` (
  `id_mission` int(11) NOT NULL,
  `id_organisation` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `nb_places` int(11) NOT NULL,
  `statut` enum('ouverte','fermée','en attente') DEFAULT 'ouverte',
  `date_creation` datetime DEFAULT current_timestamp(),
  `handicap` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mission_competence`
--

CREATE TABLE `mission_competence` (
  `id_mission` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `organisation`
--

CREATE TABLE `organisation` (
  `id_organisation` int(11) NOT NULL,
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
  `description_organisation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `organisation_representant`
--

CREATE TABLE `organisation_representant` (
  `id_organisation` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('1','2','3') DEFAULT '1',
  `tagUsers` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `handicap` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur_competence`
--

CREATE TABLE `utilisateur_competence` (
  `id_utilisateur` int(11) NOT NULL,
  `id_competence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `competence`
--
ALTER TABLE `competence`
  ADD PRIMARY KEY (`id_competence`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`id_evaluation`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_organisation` (`id_organisation`),
  ADD KEY `id_mission` (`id_mission`);

--
-- Index pour la table `favoris_mission`
--
ALTER TABLE `favoris_mission`
  ADD PRIMARY KEY (`id_utilisateur`,`id_mission`),
  ADD KEY `id_mission` (`id_mission`);

--
-- Index pour la table `favoris_organisation`
--
ALTER TABLE `favoris_organisation`
  ADD PRIMARY KEY (`id_utilisateur`,`id_organisation`),
  ADD KEY `id_organisation` (`id_organisation`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id_inscription`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `id_mission` (`id_mission`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `id_envoyeur` (`id_envoyeur`),
  ADD KEY `id_destinataire` (`id_destinataire`);

--
-- Index pour la table `mission`
--
ALTER TABLE `mission`
  ADD PRIMARY KEY (`id_mission`),
  ADD KEY `id_organisation` (`id_organisation`);

--
-- Index pour la table `mission_competence`
--
ALTER TABLE `mission_competence`
  ADD PRIMARY KEY (`id_mission`,`id_competence`),
  ADD KEY `id_competence` (`id_competence`);

--
-- Index pour la table `organisation`
--
ALTER TABLE `organisation`
  ADD PRIMARY KEY (`id_organisation`),
  ADD UNIQUE KEY `email_contact` (`email_contact`);

--
-- Index pour la table `organisation_representant`
--
ALTER TABLE `organisation_representant`
  ADD PRIMARY KEY (`id_organisation`,`id_utilisateur`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `utilisateur_competence`
--
ALTER TABLE `utilisateur_competence`
  ADD PRIMARY KEY (`id_utilisateur`,`id_competence`),
  ADD KEY `id_competence` (`id_competence`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `competence`
--
ALTER TABLE `competence`
  MODIFY `id_competence` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `id_evaluation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `inscription`
--
ALTER TABLE `inscription`
  MODIFY `id_inscription` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `mission`
--
ALTER TABLE `mission`
  MODIFY `id_mission` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `organisation`
--
ALTER TABLE `organisation`
  MODIFY `id_organisation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `evaluation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL,
  ADD CONSTRAINT `evaluation_ibfk_2` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE SET NULL,
  ADD CONSTRAINT `evaluation_ibfk_3` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE;

--
-- Contraintes pour la table `favoris_mission`
--
ALTER TABLE `favoris_mission`
  ADD CONSTRAINT `favoris_mission_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoris_mission_ibfk_2` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE;

--
-- Contraintes pour la table `favoris_organisation`
--
ALTER TABLE `favoris_organisation`
  ADD CONSTRAINT `favoris_organisation_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoris_organisation_ibfk_2` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE;

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`id_envoyeur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`id_destinataire`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mission`
--
ALTER TABLE `mission`
  ADD CONSTRAINT `mission_ibfk_1` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE;

--
-- Contraintes pour la table `mission_competence`
--
ALTER TABLE `mission_competence`
  ADD CONSTRAINT `mission_competence_ibfk_1` FOREIGN KEY (`id_mission`) REFERENCES `mission` (`id_mission`) ON DELETE CASCADE,
  ADD CONSTRAINT `mission_competence_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competence` (`id_competence`) ON DELETE CASCADE;

--
-- Contraintes pour la table `organisation_representant`
--
ALTER TABLE `organisation_representant`
  ADD CONSTRAINT `organisation_representant_ibfk_1` FOREIGN KEY (`id_organisation`) REFERENCES `organisation` (`id_organisation`) ON DELETE CASCADE,
  ADD CONSTRAINT `organisation_representant_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur_competence`
--
ALTER TABLE `utilisateur_competence`
  ADD CONSTRAINT `utilisateur_competence_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `utilisateur_competence_ibfk_2` FOREIGN KEY (`id_competence`) REFERENCES `competence` (`id_competence`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
