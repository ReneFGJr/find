-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 12, 2019 at 10:09 AM
-- Server version: 5.7.11
-- PHP Version: 7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `library_place`
--

CREATE TABLE `library_place` (
  `id_lp` bigint(20) UNSIGNED NOT NULL,
  `lp_name` char(100) COLLATE utf8_bin NOT NULL,
  `lp_address` text COLLATE utf8_bin NOT NULL,
  `lp_coord_x` float NOT NULL DEFAULT '0',
  `lp_coord_y` float NOT NULL DEFAULT '0',
  `lp_email` char(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lp_LIBRARY` int(11) NOT NULL,
  `lp_contato` char(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lp_responsavel` char(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lp_telefone` char(20) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `library_place`
--
ALTER TABLE `library_place`
  ADD UNIQUE KEY `id_lp` (`id_lp`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `library_place`
--
ALTER TABLE `library_place`
  MODIFY `id_lp` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
