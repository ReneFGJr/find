-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 24, 2020 at 08:59 PM
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
-- Table structure for table `find_item`
--

CREATE TABLE `find_item` (
  `id_i` bigint(20) UNSIGNED NOT NULL,
  `i_tombo` int(11) NOT NULL DEFAULT '0',
  `i_status` int(11) NOT NULL DEFAULT '0',
  `i_aquisicao` int(11) NOT NULL DEFAULT '0',
  `i_type` int(11) NOT NULL,
  `i_identifier` char(15) COLLATE utf8_bin NOT NULL,
  `i_uri` char(100) COLLATE utf8_bin NOT NULL,
  `i_library` int(11) NOT NULL,
  `i_library_place` int(11) NOT NULL,
  `i_library_classification` int(11) NOT NULL,
  `i_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `i_ip` char(20) COLLATE utf8_bin NOT NULL,
  `i_usuario` int(11) NOT NULL,
  `i_dt_emprestimo` date NOT NULL,
  `i_dt_prev` int(11) NOT NULL,
  `i_dt_renovavao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `find_item`
--
ALTER TABLE `find_item`
  ADD UNIQUE KEY `id_i` (`id_i`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `find_item`
--
ALTER TABLE `find_item`
  MODIFY `id_i` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
