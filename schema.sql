SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `documents` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `SlugId` varchar(16) NOT NULL,
  `Filename` varchar(120) NOT NULL,
  `Public` tinyint(1) NOT NULL,
  `DeleteKey` varchar(32) NOT NULL,
  `Views` bigint(20) unsigned NOT NULL DEFAULT '0',
  `OriginalFilename` varchar(256) NOT NULL,
  `Uploaded` timestamp NULL DEFAULT NULL,
  `Mirrored` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `SlugId` (`SlugId`),
  KEY `Uploaded` (`Uploaded`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
