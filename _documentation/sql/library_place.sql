-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 20-Mar-2022 às 22:52
-- Versão do servidor: 5.7.31
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
-- Estrutura da tabela `library_place`
--

DROP TABLE IF EXISTS `library_place`;
CREATE TABLE IF NOT EXISTS `library_place` (
  `id_lp` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lp_name` char(100) COLLATE utf8_bin NOT NULL,
  `lp_address` text COLLATE utf8_bin NOT NULL,
  `lp_coord_x` float NOT NULL DEFAULT '0',
  `lp_coord_y` float NOT NULL DEFAULT '0',
  `lp_email` char(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lp_LIBRARY` int(11) NOT NULL,
  `lp_contato` char(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lp_responsavel` char(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lp_telefone` char(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `lp_site` char(100) COLLATE utf8_bin NOT NULL,
  `lp_obs` text COLLATE utf8_bin NOT NULL,
  `lp_active` int(11) NOT NULL DEFAULT '1',
  `lp_class_type` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_lp` (`id_lp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
