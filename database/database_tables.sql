-- MySQL dump 10.13  Distrib 5.5.61, for FreeBSD11.1 (amd64)
--
-- Host: localhost    Database: gamesarchive
-- ------------------------------------------------------
-- Server version	5.5.61

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2074 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compilations`
--

DROP TABLE IF EXISTS `compilations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compilations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_downloads`
--

DROP TABLE IF EXISTS `game_downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_downloads` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `downloads` int(11) NOT NULL DEFAULT '0',
  `gamepages` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_genre`
--

DROP TABLE IF EXISTS `game_genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game_genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL,
  `genreid` int(11) NOT NULL,
  `ord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gameid` (`gameid`,`genreid`)
) ENGINE=InnoDB AUTO_INCREMENT=10576 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT NULL,
  `title_article` varchar(80) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `year` varchar(4) DEFAULT NULL,
  `genre` int(11) DEFAULT NULL,
  `reltype` char(1) DEFAULT NULL,
  `notes` mediumtext,
  `players_min` int(11) NOT NULL DEFAULT '1',
  `players_max` int(11) NOT NULL DEFAULT '1',
  `joystick` char(1) DEFAULT NULL,
  `save` char(1) DEFAULT NULL,
  `hardware` varchar(255) DEFAULT NULL COMMENT 'Hardware required',
  `electron` char(1) DEFAULT NULL COMMENT 'Electron game changed to work on BBC.',
  `version` varchar(255) DEFAULT NULL,
  `series` varchar(40) DEFAULT NULL,
  `series_no` varchar(20) DEFAULT NULL,
  `lastupdater` int(11) NOT NULL,
  `lastupdated` datetime NOT NULL,
  `created` datetime NOT NULL,
  `creator` int(11) NOT NULL,
  `imgupdated` datetime DEFAULT NULL,
  `imgupdater` int(11) NOT NULL,
  `compat_a` char(1) DEFAULT 'N' COMMENT 'Model A Compatibility',
  `compat_b` char(1) DEFAULT 'Y' COMMENT 'Model B Compatibility',
  `compat_master` char(1) DEFAULT NULL COMMENT 'Master Compatibility',
  PRIMARY KEY (`id`),
  KEY `reltype` (`reltype`),
  CONSTRAINT `reltype` FOREIGN KEY (`reltype`) REFERENCES `reltype` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4286 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games_authors`
--

DROP TABLE IF EXISTS `games_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `games_id` int(11) NOT NULL DEFAULT '0',
  `authors_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `games_id` (`games_id`),
  KEY `authors_id` (`authors_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7467 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games_compilations`
--

DROP TABLE IF EXISTS `games_compilations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games_compilations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `games_id` int(11) NOT NULL,
  `compilations_id` int(11) NOT NULL,
  `ord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `games_id` (`games_id`,`compilations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1288 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games_publishers`
--

DROP TABLE IF EXISTS `games_publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `games_publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL,
  `pubid` int(11) NOT NULL,
  `main` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `pubid` (`pubid`)
) ENGINE=InnoDB AUTO_INCREMENT=6182 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=696 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) DEFAULT NULL,
  `subdir` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `probs` char(1) NOT NULL DEFAULT 'O',
  `customurl` varchar(255) DEFAULT NULL,
  `main` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `main` (`main`)
) ENGINE=InnoDB AUTO_INCREMENT=4171 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `importgames`
--

DROP TABLE IF EXISTS `importgames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `importgames` (
  `disc` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `commercial` varchar(30) DEFAULT NULL,
  `genre1` varchar(255) DEFAULT NULL,
  `genre2` varchar(255) DEFAULT NULL,
  `year` varchar(5) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `problems` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `save` varchar(255) DEFAULT NULL,
  `joystick` varchar(255) DEFAULT NULL,
  `compilation` varchar(255) DEFAULT NULL,
  `seriesno` varchar(255) DEFAULT NULL,
  `playersmin` varchar(255) DEFAULT NULL,
  `playersmax` varchar(255) DEFAULT NULL,
  `passwords` varchar(255) DEFAULT NULL,
  `specialr` varchar(255) DEFAULT NULL,
  `electron` varchar(255) DEFAULT NULL,
  `version` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `publishers`
--

DROP TABLE IF EXISTS `publishers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=754 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reltype`
--

DROP TABLE IF EXISTS `reltype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reltype` (
  `id` char(1) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `selected` char(1) NOT NULL,
  `rel_order` int(13) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `screenshots`
--

DROP TABLE IF EXISTS `screenshots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `screenshots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) DEFAULT NULL,
  `subdir` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `main` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`,`main`)
) ENGINE=InnoDB AUTO_INCREMENT=4997 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `locked` char(1) NOT NULL,
  `email` varchar(130) NOT NULL,
  `pwhash` varchar(255) DEFAULT NULL,
  `lastupdater` int(11) NOT NULL,
  `lastupdated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-01-14 13:28:37
