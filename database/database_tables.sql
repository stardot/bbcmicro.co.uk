--
-- Table structure for table `authors`
--

DROP TABLE IF EXISTS `authors`;

CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2074 DEFAULT CHARSET=utf8;

--
-- Table structure for table `compilations`
--

DROP TABLE IF EXISTS `compilations`;

CREATE TABLE `compilations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=253 DEFAULT CHARSET=utf8;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` varchar(2000) DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` VARCHAR(1) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `game_downloads`
--

DROP TABLE IF EXISTS `game_downloads`;

CREATE TABLE `game_downloads` (
  `id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `downloads` int(11) NOT NULL DEFAULT '0',
  `gamepages` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `game_genre`
--

DROP TABLE IF EXISTS `game_genre`;

CREATE TABLE `game_genre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL,
  `genreid` int(11) NOT NULL,
  `ord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gameid` (`gameid`,`genreid`)
) ENGINE=InnoDB AUTO_INCREMENT=10576 DEFAULT CHARSET=utf8;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;

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
  `imgupdater` int(11) NOT NULL DEFAULT '0',
  `compat_a` char(1) DEFAULT 'N' COMMENT 'Model A Compatibility',
  `compat_b` char(1) DEFAULT 'Y' COMMENT 'Model B Compatibility',
  `compat_master` char(1) DEFAULT NULL COMMENT 'Master Compatibility',
  `jsbeebplatform` varchar(30) NULL,
  PRIMARY KEY (`id`),
  KEY `reltype` (`reltype`),
  CONSTRAINT `reltype` FOREIGN KEY (`reltype`) REFERENCES `reltype` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4286 DEFAULT CHARSET=utf8;

--
-- Table structure for table `games_authors`
--

DROP TABLE IF EXISTS `games_authors`;

CREATE TABLE `games_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `games_id` int(11) NOT NULL DEFAULT '0',
  `authors_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `games_id` (`games_id`),
  KEY `authors_id` (`authors_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7467 DEFAULT CHARSET=utf8;

--
-- Table structure for table `games_compilations`
--

DROP TABLE IF EXISTS `games_compilations`;

CREATE TABLE `games_compilations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `games_id` int(11) NOT NULL,
  `compilations_id` int(11) NOT NULL,
  `ord` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `games_id` (`games_id`,`compilations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1288 DEFAULT CHARSET=utf8;

--
-- Table structure for table `games_publishers`
--

DROP TABLE IF EXISTS `games_publishers`;

CREATE TABLE `games_publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL,
  `pubid` int(11) NOT NULL,
  `main` char(1) NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`),
  KEY `pubid` (`pubid`)
) ENGINE=InnoDB AUTO_INCREMENT=6182 DEFAULT CHARSET=utf8;

--
-- Table structure for table `genres`
--

DROP TABLE IF EXISTS `genres`;

CREATE TABLE `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=696 DEFAULT CHARSET=utf8;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;

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

--
-- Table structure for table `importgames`
--

DROP TABLE IF EXISTS `importgames`;

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

--
-- Table structure for table `publishers`
--

DROP TABLE IF EXISTS `publishers`;

CREATE TABLE `publishers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=754 DEFAULT CHARSET=utf8;

--
-- Table structure for table `reltype`
--

DROP TABLE IF EXISTS `reltype`;

CREATE TABLE `reltype` (
  `id` char(1) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `selected` char(1) NOT NULL,
  `rel_order` int(13) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `screenshots`
--

DROP TABLE IF EXISTS `screenshots`;

CREATE TABLE `screenshots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gameid` int(11) DEFAULT NULL,
  `subdir` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `main` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gameid` (`gameid`,`main`)
) ENGINE=InnoDB AUTO_INCREMENT=4997 DEFAULT CHARSET=utf8;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;

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

--
-- Table structure for table `game_keys`
--

DROP TABLE IF EXISTS `game_keys`;

CREATE TABLE `game_keys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gameid` int NOT NULL,
  `jsbeebbrowserkey` varchar(30) NOT NULL,
  `jsbeebgamekey` varchar(30) NOT NULL,
  `keydescription` varchar(255) NOT NULL,
  `keyname` varchar(30) NOT NULL,
  `rel_order` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `highlights`
--

DROP TABLE IF EXISTS `highlights`;

CREATE TABLE `highlights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `games_id` int(11) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `random` tinyint(1) DEFAULT 0,
  `visible` tinyint(1) DEFAULT 0,
  `colour` varchar(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(1000) DEFAULT NULL,
  `heading` varchar(255) DEFAULT NULL,
  `screenshot_url` varchar(1000) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `position` int(11) DEFAULT 0,
  `download_button` tinyint(1) DEFAULT 0,
  `play_button` tinyint(1) DEFAULT 0,
  `show_publisher` tinyint(1) DEFAULT 0,
  `show_year` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
