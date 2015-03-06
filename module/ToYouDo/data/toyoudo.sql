-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 28. Nov 2014 um 20:30
-- Server Version: 5.5.38-0ubuntu0.14.04.1
-- PHP-Version: 5.5.17-2+deb.sury.org~precise+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `toyoudo`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `friend`
--

CREATE TABLE IF NOT EXISTS `friend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_userId` int(10) unsigned NOT NULL,
  `friend_userId` int(10) unsigned NOT NULL,
  `accepted` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `friend_UNIQUE` (`user_userId`,`friend_userId`),
  KEY `fk_friend_2_idx` (`friend_userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `item`
--

CREATE TABLE IF NOT EXISTS `item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listId` int(10) unsigned NOT NULL,
  `content` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_item_1_idx` (`listId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `todo_list`
--

CREATE TABLE IF NOT EXISTS `todo_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `friendId` int(10) unsigned DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `deadline` date DEFAULT NULL,
  `date` datetime NOT NULL,
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_list_1_idx` (`userId`),
  KEY `fk_list_2_idx` (`friendId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `state` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `display_name`, `password`, `state`) VALUES
(1, 'pateta', 'pateta@hotmail.de', 'Pateta', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(2, 'magrathea', 'magrathea@hotmail.de', 'Magrathea', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(3, 'head', 'head@hotmail.de', 'Head', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(4, 'xineca', 'xineca@hotmail.de', 'Xineca', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(5, 'brekler', 'brekler@hotmail.de', 'Brekler', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(6, 'stürmer', 'stürmer@hotmail.de', 'Stürmer', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(7, 'abseits', 'abseits@hotmail.de', 'Abseits', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(8, 'hughes', 'hughes@hotmail.de', 'Hughes', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0),
(9, 'dumont', 'dumont@hotmail.de', 'Dumont', '$2y$14$lgRgrmoOJZdti7R7JoOS/udjW2CIBVp8y8OT8R86bK6G89EbslPSS', 0);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `friend`
--
ALTER TABLE `friend`
  ADD CONSTRAINT `fk_friend_1` FOREIGN KEY (`friend_userId`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_friend_2` FOREIGN KEY (`user_userId`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `fk_item_1` FOREIGN KEY (`listId`) REFERENCES `todo_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `todo_list`
--
ALTER TABLE `todo_list`
  ADD CONSTRAINT `fk_list_1` FOREIGN KEY (`userId`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_list_2` FOREIGN KEY (`friendId`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
