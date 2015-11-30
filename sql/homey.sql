-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 30, 2015 at 03:54 PM
-- Server version: 5.5.46-0+deb8u1
-- PHP Version: 5.6.14-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
  `SupportsStatus` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `Name`, `Housecode`, `Unitcode`, `LastState`, `LastModified`, `SupportsStatus`) VALUES
(0000000000, 'TV+Satellite', 'm', 7, 1, 1448898754, 0),
(0000000001, 'Szamitogep', 'o', 2, 1, 1448898754, 0),
(0000000002, 'Server', 'k', 4, 1, 1448898754, 0);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `Deviceid` int(11) NOT NULL,
  `LastState` int(11) NOT NULL,
  `LastModified` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `log`
--

INSERT INTO `log` (`Deviceid`, `LastState`, `LastModified`) VALUES
(0, 1, 1448898754),
(1, 1, 1448898754),
(2, 1, 1448898754);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
 ADD UNIQUE KEY `id` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */; 
