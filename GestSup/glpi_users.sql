-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  lun. 09 juil. 2018 à 14:52
-- Version du serveur :  10.1.32-MariaDB
-- Version de PHP :  7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `basesupport`
--

-- --------------------------------------------------------

--
-- Structure de la table `glpi_users`
--

CREATE TABLE `glpi_users` (
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `IP_WAN` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_cat` int(11) DEFAULT NULL,
  `id_place` int(11) DEFAULT NULL,
  `id_group` int(11) DEFAULT NULL,
  `glpi` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `glpi_users`
--

INSERT INTO `glpi_users` (`name`, `IP_WAN`, `id_cat`, `id_place`, `id_group`, `glpi`) VALUES
('artemis', '128.79.56.227', 8, 7, 2, 1),
('apaehm-esat', NULL, 15, 30, 10, 1),
('apaehm-foyer', NULL, 15, 31, 10, 1),
('apaehm-ime', NULL, 15, 28, 10, 1),
('apaehm-mas', NULL, 15, 29, 10, 1),
('apaehm-sessad', NULL, 15, 32, 10, 1),
('asa-siege', NULL, 6, 5, 4, 0),
('bors', NULL, 16, 33, 11, 0),
('ccbta-ateliers', NULL, 12, 19, 7, 1),
('ccbta-ciap', NULL, 12, 20, 7, 1),
('ccbta-musee', NULL, 12, 21, 7, 1),
('ccbta-port', NULL, 12, 18, 7, 1),
('ccbta-siege', NULL, 12, 17, 7, 1),
('ccpu-alsh', NULL, 10, 100007, 5, 0),
('ccpu-alsh-blauzac', NULL, 10, 100010, 5, 0),
('ccpu-alsh-garrigues', NULL, 10, 100011, 5, 0),
('ccpu-alsh-moussac', NULL, 10, 100009, 5, 0),
('ccpu-alsh-uzes', NULL, 10, 100008, 5, 0),
('ccpu-creche-foissac', NULL, 10, 14, 5, 0),
('ccpu-creche-bruguiere', NULL, 10, 12, 5, 0),
('ccpu-creche-stquentin', NULL, 10, 11, 5, 0),
('ccpu-creche-uzes', NULL, 10, 10, 5, 0),
('ccpu-mediatheque-uzes', NULL, 10, 9, 5, 0),
('ccpu-ram', NULL, 10, 13, 5, 0),
('ccpu-siege', NULL, 10, 8, 5, 0),
('fourques-mairie', NULL, 7, 6, 13, 0),
('golf-nimes', NULL, 5, 4, 1, 0),
('golf-servanes', NULL, 17, 34, 12, 0),
('grapsud-beziers', NULL, 9, 15, 3, 0),
('grapsud-cruviers', NULL, 9, 2, 3, 0),
('grapsud-lacrau', NULL, 9, 3, 3, 0),
('grapsud-rafidin', NULL, 9, 100005, 3, 0),
('grapsud-rieux', NULL, 9, 1, 3, 0),
('grapsud-sigolsheim', NULL, 9, 100006, 3, 0),
('ifcl-siege', NULL, 19, 100002, 15, 0),
('mareva-siege', '176.157.135.145', 20, 100003, 17, 1),
('otuzes', NULL, 14, 27, 9, 0),
('pareva-siege', NULL, 21, 100004, 18, 0),
('Localhost', '::1', NULL, NULL, NULL, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `glpi_users`
--
ALTER TABLE `glpi_users`
  ADD UNIQUE KEY `unicity` (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
