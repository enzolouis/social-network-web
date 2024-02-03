-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 02 fév. 2024 à 18:02
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `socialnetwork`
--

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `sender` varchar(50) NOT NULL,
  `receiver` varchar(50) NOT NULL,
  `sentDate` date NOT NULL,
  `sentHour` time NOT NULL,
  `content` varchar(500) NOT NULL,
  `liked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`sender`, `receiver`, `sentDate`, `sentHour`, `content`, `liked`) VALUES
('xouxou', 'Nautilus', '2024-02-02', '17:20:37', 'Tu décryptes ?', 0),
('Nautilus', 'xouxou', '2024-02-02', '17:22:04', 'Mdrr', 0),
('xouxou', 'Nautilus', '2024-02-02', '17:22:31', '?', 0),
('Nautilus', 'xouxou', '2024-02-02', '17:23:10', 'Nooon', 0),
('xouxou', 'Nautilus', '2024-02-02', '17:24:45', 'Ok', 0),
('xouxou', 'Nautilus', '2024-02-02', '17:24:58', 'Salut j\'te bloque', 0),
('Nautilus', 'xouxou', '2024-02-02', '17:25:51', 'Okk bye', 0),
('xouxou', 'Nautilus', '2024-02-02', '17:26:15', 'Ici ça décrypte', 0);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `login` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `description` varchar(400) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`login`, `username`, `password`, `description`) VALUES
('xouxou', 'Maxence Maury-Balit', '$2y$10$rywwfYzodOnEaCtervdoneG7H8ny3PpS0yaij6xxGPIc6M4QSVjUC', 'Wise mystical tree enjoyer'),
('Nautilus', 'Zoubairov Ibrahim', '$2y$10$2f1gUoTlar/foCtSsyonmex4fekOJ03YtnlkbInnBDw4xp4oLE8vq', 'NOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOON');
/*
('ember', 'Loup & Enzo', 'nzo', 'C\'est fondamental'),
('pgwk', 'Pempem', 'zzz', 'simple and clean (ray of hope mix)'),
('Matic', 'Willy Will', 'dz', 'Je suis ne*rophobe');*/

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`login`);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message_sender` FOREIGN KEY (`sender`) REFERENCES `user` (`login`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
