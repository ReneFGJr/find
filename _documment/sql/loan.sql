-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 14, 2019 at 11:17 AM
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
-- Table structure for table `loan`
--

CREATE TABLE `loan` (
  `id_l` bigint(20) UNSIGNED NOT NULL,
  `l_date` date NOT NULL,
  `l_time` char(8) COLLATE utf8_bin NOT NULL,
  `l_status` int(11) NOT NULL DEFAULT '-1',
  `l_user` int(15) NOT NULL,
  `l_log` int(11) NOT NULL,
  `l_ip` char(16) COLLATE utf8_bin NOT NULL,
  `l_auth` int(11) NOT NULL,
  `l_email` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `loan_item`
--

CREATE TABLE `loan_item` (
  `id_li` bigint(20) UNSIGNED NOT NULL,
  `li_tombo` char(15) COLLATE utf8_bin NOT NULL,
  `li_prev_dev` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loan`
--
ALTER TABLE `loan`
  ADD UNIQUE KEY `id_l` (`id_l`);

--
-- Indexes for table `loan_item`
--
ALTER TABLE `loan_item`
  ADD UNIQUE KEY `id_li` (`id_li`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loan`
--
ALTER TABLE `loan`
  MODIFY `id_l` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `loan_item`
--
ALTER TABLE `loan_item`
  MODIFY `id_li` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
