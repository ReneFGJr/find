-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 20, 2020 at 09:12 PM
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
-- Table structure for table `find_expression`
--

CREATE TABLE `find_expression` (
  `id_e` bigint(20) UNSIGNED NOT NULL,
  `e_work` int(11) NOT NULL,
  `e_language` char(5) NOT NULL,
  `e_type` char(5) NOT NULL,
  `e_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `find_expression`
--

INSERT INTO `find_expression` (`id_e`, `e_work`, `e_language`, `e_type`, `e_created`) VALUES
(1, 1, 'pt', 'book', '2020-01-20 20:14:42'),
(2, 0, 'pt', 'book', '2020-01-20 20:36:53');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `find_manifestation`
--

INSERT INTO `find_manifestation` (`id_m`, `m_expression`, `m_isbn13`, `m_edition`, `m_year`) VALUES
(3, 1, '9788573598278', 0, 2009);

-- --------------------------------------------------------

--
-- Table structure for table `find_work`
--

CREATE TABLE `find_work` (
  `id_w` bigint(20) UNSIGNED NOT NULL,
  `w_title` text NOT NULL,
  `w_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `w_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `find_work`
--

INSERT INTO `find_work` (`id_w`, `w_title`, `w_created`, `w_id`) VALUES
(1, 'Inclusão digital e empregabilidade', '2020-01-20 19:55:11', 0);

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
  MODIFY `id_e` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `find_manifestation`
--
ALTER TABLE `find_manifestation`
  MODIFY `id_m` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `find_work`
--
ALTER TABLE `find_work`
  MODIFY `id_w` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
