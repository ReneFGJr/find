-- phpMyAdmin SQL Dump
-- version 5.0.0-alpha1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 21, 2020 at 11:38 AM
-- Server version: 8.0.18-0ubuntu0.19.10.1
-- PHP Version: 7.3.11-0ubuntu0.19.10.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
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
-- Table structure for table `find_manifestation`
--

CREATE TABLE `find_manifestation` (
  `id_m` bigint(20) UNSIGNED NOT NULL,
  `m_expression` int(11) NOT NULL,
  `m_isbn13` char(13) COLLATE utf8_bin NOT NULL,
  `m_edition` int(11) NOT NULL DEFAULT '0',
  `m_year` int(11) NOT NULL DEFAULT '0',
  `m_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `find_manifestation`
--
ALTER TABLE `find_manifestation`
  ADD UNIQUE KEY `id_m` (`id_m`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `find_manifestation`
--
ALTER TABLE `find_manifestation`
  MODIFY `id_m` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

