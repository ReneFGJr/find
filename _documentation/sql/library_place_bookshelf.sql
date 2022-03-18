-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 18-Mar-2022 às 17:05
-- Versão do servidor: 10.4.13-MariaDB
-- versão do PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `find`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `library_place_bookshelf`
--

DROP TABLE IF EXISTS `library_place_bookshelf`;
CREATE TABLE IF NOT EXISTS `library_place_bookshelf` (
  `id_bs` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bs_name` char(100) COLLATE utf8_bin NOT NULL,
  `bs_image` char(100) COLLATE utf8_bin NOT NULL,
  `bs_bs` text COLLATE utf8_bin NOT NULL,
  `bs_LIBRARY` int(11) NOT NULL DEFAULT 0,
  UNIQUE KEY `id_bs` (`id_bs`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
