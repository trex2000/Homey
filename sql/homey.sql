-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 05, 2014 at 06:43 PM
-- Server version: 5.5.31
-- PHP Version: 5.4.4-14+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `homey`
--

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE IF NOT EXISTS `devices` (
  `id` int(10) unsigned zerofill DEFAULT NULL COMMENT 'index',
  `Name` varchar(32) DEFAULT NULL COMMENT 'Name of device',
  `Housecode` varchar(1) DEFAULT NULL,
  `Unitcode` int(15) DEFAULT NULL,
  `LastState` tinyint(1) DEFAULT NULL,
  `LastModified` int(11) DEFAULT NULL,
  `SupportsStatus` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `Name`, `Housecode`, `Unitcode`, `LastState`, `LastModified`, `SupportsStatus`) VALUES
(0000000000, 'TV+Satellite', 'm', 7, 1, 1397973739, 0),
(0000000001, 'Szamitogep', 'o', 2, 1, 1398012225, 0),
(0000000002, 'Server', 'k', 4, 1, 1398586692, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
