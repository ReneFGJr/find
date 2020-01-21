-- phpMyAdmin SQL Dump
-- version 5.0.0-alpha1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 21, 2020 at 12:01 AM
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
-- Table structure for table `find_expression`
--

CREATE TABLE `find_expression` (
  `id_e` bigint(20) UNSIGNED NOT NULL,
  `e_work` int(11) NOT NULL,
  `e_language` char(5) NOT NULL,
  `e_type` char(5) NOT NULL,
  `e_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `find_manifestation`
--

CREATE TABLE `find_manifestation` (
  `id_m` bigint(20) UNSIGNED NOT NULL,
  `m_expression` int(11) NOT NULL,
  `m_isbn13` char(13) NOT NULL,
  `m_edition` int(11) NOT NULL DEFAULT '0',
  `m_year` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `find_manifestation_url`
--

CREATE TABLE `find_manifestation_url` (
  `id_mu` bigint(20) UNSIGNED NOT NULL,
  `mu_url` text NOT NULL,
  `mu_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `find_work`
--

CREATE TABLE `find_work` (
  `id_w` bigint(20) UNSIGNED NOT NULL,
  `w_title` text NOT NULL,
  `w_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `w_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `find_expression`
--
ALTER TABLE `find_expression`
  ADD UNIQUE KEY `id_e` (`id_e`);

--
-- Indexes for table `find_manifestation`
--
ALTER TABLE `find_manifestation`
  ADD UNIQUE KEY `id_m` (`id_m`);

--
-- Indexes for table `find_manifestation_url`
--
ALTER TABLE `find_manifestation_url`
  ADD UNIQUE KEY `id_mu` (`id_mu`);

--
-- Indexes for table `find_work`
--
ALTER TABLE `find_work`
  ADD UNIQUE KEY `id_w` (`id_w`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `find_expression`
--
ALTER TABLE `find_expression`
  MODIFY `id_e` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `find_manifestation`
--
ALTER TABLE `find_manifestation`
  MODIFY `id_m` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `find_manifestation_url`
--
ALTER TABLE `find_manifestation_url`
  MODIFY `id_mu` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `find_work`
--
ALTER TABLE `find_work`
  MODIFY `id_w` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

