-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 30, 2025 at 10:23 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `find`
--

-- --------------------------------------------------------

--
-- Table structure for table `rdf_form_class_2`
--

DROP TABLE IF EXISTS `rdf_form_class_2`;
CREATE TABLE IF NOT EXISTS `rdf_form_class_2` (
  `id_form` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `form_frbr` char(1) DEFAULT NULL,
  `form_property` int NOT NULL,
  `form_group` char(10) NOT NULL,
  `form_group_subgroup` char(20) DEFAULT NULL,
  `form_library` char(4) NOT NULL,
  `form_order` int NOT NULL DEFAULT '0',
  UNIQUE KEY `id_form` (`id_form`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rdf_form_class_2`
--

INSERT INTO `rdf_form_class_2` (`id_form`, `form_frbr`, `form_property`, `form_group`, `form_group_subgroup`, `form_library`, `form_order`) VALUES
(1, 'W', 17, 'TITLE', NULL, '1000', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
