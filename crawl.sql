-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 25 oct. 2023 à 21:44
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `crawl`
--

-- --------------------------------------------------------

--
-- Structure de la table `crawl_results`
--

CREATE TABLE `crawl_results` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `crawl_results`
--

INSERT INTO `crawl_results` (`id`, `url`, `timestamp`) VALUES
(26, 'https://www.pereine.com/welcome-crm', '2023-10-25 20:37:26'),
(27, 'https://www.pereine.com/#', '2023-10-25 20:37:26'),
(28, 'https://www.pereine.com/privacy', '2023-10-25 20:37:26'),
(29, 'https://www.pereine.com/welcome-crm', '2023-10-25 20:37:26'),
(30, 'https://www.pereine.com/welcome-ecommerce', '2023-10-25 20:37:26'),
(31, 'https://www.pereine.com/#', '2023-10-25 20:37:26'),
(32, 'https://www.pereine.com/#', '2023-10-25 20:37:26'),
(33, 'https://www.pereine.com/', '2023-10-25 20:37:26'),
(34, 'https://www.pereine.com/', '2023-10-25 20:37:26'),
(35, 'https://www.pereine.com/', '2023-10-25 20:37:26');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `crawl_results`
--
ALTER TABLE `crawl_results`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `crawl_results`
--
ALTER TABLE `crawl_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
