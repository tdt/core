-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 13, 2014 at 01:50 AM
-- Server version: 5.5.37
-- PHP Version: 5.3.10-1ubuntu3.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `beroads`
--

-- --------------------------------------------------------

--
-- Table structure for table `definitions`
--

CREATE TABLE IF NOT EXISTS `definitions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collection_uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resource_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `source_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `source_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contributor` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `format` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `relation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `coverage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rights` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cache_minutes` smallint(6) DEFAULT '5',
  `draft` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `definitions`
--

INSERT INTO `definitions` (`id`, `collection_uri`, `resource_name`, `source_type`, `source_id`, `created_at`, `updated_at`, `title`, `creator`, `subject`, `description`, `publisher`, `contributor`, `date`, `type`, `format`, `identifier`, `source`, `language`, `relation`, `coverage`, `rights`, `cache_minutes`, `draft`) VALUES
(1, 'v1/iway', 'trafficevent', 'InstalledDefinition', 1, '2014-05-05 14:27:14', '2014-05-05 17:43:48', 'Traffic events', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, 'Mobiris, Verkeers Centrum, Centre Perex, Police fédérale', 'English', NULL, NULL, 'License Not Specified', 5, 0),
(2, 'v1/iway', 'radar', 'InstalledDefinition', 2, '2014-05-05 14:27:58', '2014-05-05 17:44:15', 'Radars', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, NULL, 'English', NULL, NULL, 'License Not Specified', 5, 0),
(3, 'v1/iway', 'camera', 'InstalledDefinition', 3, '2014-05-05 14:28:59', '2014-05-05 17:46:08', 'Cameras', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, 'Mobiris, Verkeers Centrum, Centre Perex', 'English', NULL, NULL, 'License Not Specified', 5, 0),
(4, 'v1/iway', 'forecast', 'InstalledDefinition', 4, '2014-05-05 14:29:44', '2014-05-05 17:44:44', 'Forecast', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, NULL, 'English', NULL, NULL, 'License Not Specified', 5, 0),
(6, 'v2/iway', 'trafficevent', 'InstalledDefinition', 6, '2014-05-05 17:12:33', '2014-05-05 17:45:36', 'Traffic events', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, 'Mobiris, Verkeers Centrum, Centre Perex, Federal police', 'English', NULL, NULL, 'License Not Specified', 5, 0),
(7, 'v2/iway', 'camera', 'InstalledDefinition', 7, '2014-05-05 17:14:16', '2014-05-05 17:46:03', 'Cameras', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, 'Mobiris, Verkeers Centrum, Centre Perex', 'English', NULL, NULL, 'License Not Specified', 5, 0),
(8, 'v2/iway', 'radar', 'InstalledDefinition', 8, '2014-05-05 17:14:58', '2014-05-05 17:46:34', 'Radars', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, NULL, 'English', NULL, NULL, 'License Not Specified', 5, 0),
(9, 'v2/iway', 'forecast', 'InstalledDefinition', 9, '2014-05-05 17:15:31', '2014-05-05 17:47:03', 'Forecast', NULL, NULL, NULL, NULL, NULL, '05/05/2014', NULL, NULL, NULL, NULL, 'English', NULL, NULL, 'License Not Specified', 5, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
