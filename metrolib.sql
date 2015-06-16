CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `artist` int(11) DEFAULT NULL,
  `year` varchar(4) DEFAULT NULL,
  `num_of_tracks` int(11) DEFAULT NULL,
  `genre` varchar(20) DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `image_ref` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table metrolib.artists
CREATE TABLE IF NOT EXISTS `artists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `char` varchar(1) NOT NULL,
  `link` varchar(100) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table metrolib.featured
CREATE TABLE IF NOT EXISTS `featured` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `link` varchar(100) DEFAULT NULL,
  `album` int(11) DEFAULT NULL,
  `artist` int(11) DEFAULT NULL,
  `featured` int(11) DEFAULT NULL,
  `master_featured` varchar(150) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `content` text,
  `writer` tinytext,
  `publisher` tinytext,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table metrolib.lyrics
CREATE TABLE IF NOT EXISTS `lyrics` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `link` varchar(100) DEFAULT NULL,
  `album` int(11) DEFAULT NULL,
  `artist` int(11) DEFAULT NULL,
  `master_featured` int(11) DEFAULT NULL,
  `featured` int(11) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `content` text,
  `writer` tinytext,
  `publisher` tinytext,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
