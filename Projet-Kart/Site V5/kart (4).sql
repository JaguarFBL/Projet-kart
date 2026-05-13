-- phpMyAdmin SQL Dump
-- version 5.2.2deb1+deb13u1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mer. 13 mai 2026 à 06:59
-- Version du serveur : 11.8.6-MariaDB-0+deb13u1 from Debian
-- Version de PHP : 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `kart`
--

-- --------------------------------------------------------

--
-- Structure de la table `actif`
--

CREATE TABLE `actif` (
  `ID` int(11) NOT NULL DEFAULT 1,
  `pilote` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `capteur`
--

CREATE TABLE `capteur` (
  `ID` int(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `temperaturepiste` int(11) DEFAULT NULL,
  `humiditepiste` int(11) DEFAULT NULL,
  `temperaturebatterie` int(11) DEFAULT NULL,
  `tensionbatterie` int(11) DEFAULT NULL,
  `pourcentagebatterie` int(11) DEFAULT NULL,
  `intensitebatterie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `capteur`
--

INSERT INTO `capteur` (`ID`, `date`, `temperaturepiste`, `humiditepiste`, `temperaturebatterie`, `tensionbatterie`, `pourcentagebatterie`, `intensitebatterie`) VALUES
(1074, '2026-04-01 07:54:34', 22, 68, 22, NULL, NULL, 20),
(1075, '2026-05-06 06:39:08', 22, 68, 24, 48, 39, 20),
(1076, '2026-05-06 12:16:49', 22, 52, 24, 48, 34, 20),
(1077, '2026-05-06 14:18:04', NULL, NULL, 20, 53, 89, 0),
(1078, '2026-05-06 14:18:09', NULL, NULL, 20, 53, 89, 0),
(1079, '2026-05-06 14:18:14', NULL, NULL, 20, 53, 89, 0),
(1080, '2026-05-06 14:18:19', NULL, NULL, 20, 53, 89, 0),
(1081, '2026-05-06 14:18:24', NULL, NULL, 20, 53, 89, 0),
(1082, '2026-05-06 14:18:30', NULL, NULL, 20, 53, 89, 0),
(1083, '2026-05-06 14:18:35', NULL, NULL, 20, 53, 89, 0),
(1084, '2026-05-06 14:18:40', NULL, NULL, 20, 53, 89, 0),
(1085, '2026-05-06 14:18:45', NULL, NULL, 20, 53, 89, 0),
(1086, '2026-05-06 14:18:50', NULL, NULL, 20, 53, 89, 0),
(1087, '2026-05-06 14:18:56', NULL, NULL, 20, 53, 89, 0),
(1088, '2026-05-06 14:19:01', NULL, NULL, 20, 53, 89, 0),
(1089, '2026-05-13 06:51:55', 13, 3, 10, 48, 101, 9);

-- --------------------------------------------------------

--
-- Structure de la table `pilotes`
--

CREATE TABLE `pilotes` (
  `ID` int(11) UNSIGNED NOT NULL,
  `nom` varchar(1024) NOT NULL,
  `record` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `pilotes`
--

INSERT INTO `pilotes` (`ID`, `nom`, `record`) VALUES
(13, 'gvbyby', '6273'),
(14, 'LOUAN', '5381'),
(15, 'Noah', '5381'),
(17, 'qshxfiezhsdiwx', '5028'),
(18, 'Tom', '5031'),
(19, 'TEST', '5415'),
(20, 'Mathys', '5015'),
(21, 'bob', '5103');

-- --------------------------------------------------------

--
-- Structure de la table `session`
--

CREATE TABLE `session` (
  `ID` int(11) NOT NULL,
  `pilote` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  `temps` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `session`
--

INSERT INTO `session` (`ID`, `pilote`, `timestamp`, `temps`) VALUES
(172, 'bob', 355471, 0),
(173, 'bob', 1050298, 694827),
(174, 'bob', 1055491, 5193),
(175, 'bob', 1060610, 5119),
(176, 'bob', 1065804, 5194),
(177, 'bob', 1071004, 5200),
(178, 'bob', 1076403, 5399),
(179, 'bob', 1081532, 5129),
(180, 'bob', 1086726, 5194),
(181, 'bob', 1091959, 5233),
(182, 'bob', 1097062, 5103),
(183, 'bob', 1102394, 5332),
(184, 'bob', 1107668, 5274),
(185, 'bob', 1112860, 5192);

-- --------------------------------------------------------

--
-- Structure de la table `temps_pilotes`
--

CREATE TABLE `temps_pilotes` (
  `ID` int(11) NOT NULL,
  `pilote_id` int(11) UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `temps_ms` int(11) NOT NULL,
  `temps_formate` varchar(20) GENERATED ALWAYS AS (sec_to_time(`temps_ms` / 1000)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `capteur`
--
ALTER TABLE `capteur`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `pilotes`
--
ALTER TABLE `pilotes`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `temps_pilotes`
--
ALTER TABLE `temps_pilotes`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `pilote_id` (`pilote_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `capteur`
--
ALTER TABLE `capteur`
  MODIFY `ID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1117;

--
-- AUTO_INCREMENT pour la table `pilotes`
--
ALTER TABLE `pilotes`
  MODIFY `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `session`
--
ALTER TABLE `session`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT pour la table `temps_pilotes`
--
ALTER TABLE `temps_pilotes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `temps_pilotes`
--
ALTER TABLE `temps_pilotes`
  ADD CONSTRAINT `temps_pilotes_ibfk_1` FOREIGN KEY (`pilote_id`) REFERENCES `pilotes` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
