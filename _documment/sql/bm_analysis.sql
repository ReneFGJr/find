-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 14, 2018 at 10:37 AM
-- Server version: 5.6.20-log
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `redd`
--

-- --------------------------------------------------------

--
-- Table structure for table `bm_analysis`
--

CREATE TABLE IF NOT EXISTS `bm_analysis` (
`id_a` bigint(20) unsigned NOT NULL,
  `a_name` char(150) NOT NULL,
  `a_description` text NOT NULL,
  `a_status` int(11) NOT NULL DEFAULT '1',
  `a_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bm_analysis`
--

INSERT INTO `bm_analysis` (`id_a`, `a_name`, `a_description`, `a_status`, `a_created`) VALUES
(1, 'Teses de Ensino Religoso', 'Estudo Bibliométrico e Cocitação', 1, '2018-03-14 02:04:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bm_analysis`
--
ALTER TABLE `bm_analysis`
 ADD UNIQUE KEY `id_a` (`id_a`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bm_analysis`
--
ALTER TABLE `bm_analysis`
MODIFY `id_a` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
