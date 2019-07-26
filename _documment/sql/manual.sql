-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 26, 2019 at 05:04 PM
-- Server version: 5.6.20-log
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `propel`
--

-- --------------------------------------------------------

--
-- Table structure for table `_manual_concept`
--

CREATE TABLE IF NOT EXISTS `_manual_concept` (
`id_mc` bigint(20) unsigned NOT NULL,
  `mc_term` int(11) NOT NULL,
  `mc_creadted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mc_use` int(11) NOT NULL,
  `mc_class` char(15) COLLATE utf8_bin NOT NULL,
  `mc_rel` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `_manual_name`
--

CREATE TABLE IF NOT EXISTS `_manual_name` (
`id_m` bigint(20) unsigned NOT NULL,
  `m_txt` text COLLATE utf8_bin NOT NULL,
  `m_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `_manual_concept`
--
ALTER TABLE `_manual_concept`
 ADD UNIQUE KEY `id_mc` (`id_mc`);

--
-- Indexes for table `_manual_name`
--
ALTER TABLE `_manual_name`
 ADD UNIQUE KEY `id_m` (`id_m`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `_manual_concept`
--
ALTER TABLE `_manual_concept`
MODIFY `id_mc` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `_manual_name`
--
ALTER TABLE `_manual_name`
MODIFY `id_m` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
