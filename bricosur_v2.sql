-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mer. 07 jan. 2026 à 23:20
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
-- Base de données : `bricosur_v2`
--

-- --------------------------------------------------------

--
-- Structure de la table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `date_envoi` timestamp NOT NULL DEFAULT current_timestamp(),
  `lu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `expediteur_id`, `destinataire_id`, `service_id`, `contenu`, `date_envoi`, `lu`) VALUES
(1, 2, 3, 1, 'Bonjour monsieur c\'est DAYAWA.', '2026-01-03 09:45:17', 0),
(2, 3, 2, 1, 'Bonjour, comment allez vous ?', '2026-01-03 09:45:50', 0),
(3, 2, 3, 4, 'Bonjour monsieur DAYAWA', '2026-01-03 14:57:00', 0),
(4, 2, 3, 4, 'Desole je voulais dire stakyra', '2026-01-03 14:57:27', 0),
(5, 2, 3, 5, 'Bonjour, votre proposition ne me plait pas ', '2026-01-07 21:14:56', 0);

-- --------------------------------------------------------

--
-- Structure de la table `postulations`
--

CREATE TABLE `postulations` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `date_postulation` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` enum('EN_ATTENTE','ACCEPTE','REFUSE') DEFAULT 'EN_ATTENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `postulations`
--

INSERT INTO `postulations` (`id`, `service_id`, `provider_id`, `message`, `date_postulation`, `statut`) VALUES
(1, 1, 3, NULL, '2026-01-03 09:44:22', 'ACCEPTE'),
(3, 4, 3, NULL, '2026-01-03 14:55:04', 'ACCEPTE'),
(4, 5, 3, NULL, '2026-01-07 21:10:49', 'REFUSE');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `prix_estime` decimal(10,2) DEFAULT NULL,
  `statut` enum('OUVERT','EN_COURS','TERMINE') DEFAULT 'OUVERT',
  `date_publication` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `client_id`, `titre`, `description`, `categorie`, `prix_estime`, `statut`, `date_publication`) VALUES
(1, 2, 'Reparation salle de bain', 'Reparer', 'Plomberie', 4000.00, 'OUVERT', '2026-01-03 09:43:02'),
(4, 2, 'Peinture salon', 'je veux mon salon clean', 'Peinture', 150.00, '', '2026-01-03 14:53:05'),
(5, 2, 'Reparation du mur', '<script>alert(\'Bricosur Hacke\');</script>', 'Jardinage', 1000.00, 'OUVERT', '2026-01-07 20:52:05');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('CLIENT','PROVIDER','ADMIN') DEFAULT 'CLIENT',
  `telephone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `telephone`, `created_at`) VALUES
(2, 'DAYAWA', 'gmdayawa@gmail.com', '$2y$10$CF07/OwzksecBx.r0Yb76.ubmcj83q4XiBPL5Plp29W1aj.bO/Cv6', 'CLIENT', NULL, '2026-01-02 21:04:23'),
(3, 'Stakyra', 'gdayawa.ir2027@esaip.org', '$2y$10$c.IkAg2d1CekA0Z0YvzJCeScB5Yzb6ujh4t4KeggOEuhP4m8S5.U.', 'PROVIDER', NULL, '2026-01-02 21:44:50'),
(5, 'Administrateur', 'admin@bricosur.com', '$2y$10$TVq/7rAMVpW5s3YaN.VnbuWXkx5twQUGZLKjfYodXAqRsm18l2t1y', 'ADMIN', NULL, '2026-01-03 11:37:44');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expediteur_id` (`expediteur_id`),
  ADD KEY `destinataire_id` (`destinataire_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Index pour la table `postulations`
--
ALTER TABLE `postulations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `provider_id` (`provider_id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `postulations`
--
ALTER TABLE `postulations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `postulations`
--
ALTER TABLE `postulations`
  ADD CONSTRAINT `postulations_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `postulations_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
