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
-- Table structure for table `installeddefinitions`
--

CREATE TABLE IF NOT EXISTS `installeddefinitions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `installeddefinitions`
--

INSERT INTO `installeddefinitions` (`id`, `class`, `path`, `description`, `created_at`, `updated_at`) VALUES
(1, 'TrafficEvent', 'v1/TrafficEvent.class.php', 'TrafficEvent return the latest trafic events by region or around geographic coordinate.', '2014-05-05 14:27:14', '2014-05-05 17:20:04'),
(2, 'Radar', 'v1/Radar.class.php', 'Radar return a list of all known radars.', '2014-05-05 14:27:57', '2014-05-05 17:44:15'),
(3, 'Camera', 'v1/Camera.class.php', 'Camera return a list of all known highway webcams.', '2014-05-05 14:28:59', '2014-05-05 17:44:28'),
(4, 'Forecast', 'v1/Forecast.class.php', 'Return data about travel times and trafic jam.', '2014-05-05 14:29:44', '2014-05-05 17:44:44'),
(6, 'TrafficEvent', 'v2/TrafficEvent.class.php', 'TrafficEvent return the latest trafic events by region or around geographic coordinate.', '2014-05-05 17:12:32', '2014-05-05 17:45:36'),
(7, 'Camera', 'v2/Camera.class.php', 'Camera return a list of all known highway webcams.', '2014-05-05 17:14:16', '2014-05-05 17:46:03'),
(8, 'Radar', 'v2/Radar.class.php', 'Radar return a list of all known radars.', '2014-05-05 17:14:58', '2014-05-05 17:46:34'),
(9, 'Forecast', 'v2/Forecast.class.php', 'Return data about travel times and trafic jam.', '2014-05-05 17:15:31', '2014-05-05 17:46:44');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
