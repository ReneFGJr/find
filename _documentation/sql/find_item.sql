-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 18-Mar-2022 às 13:58
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
-- Estrutura da tabela `find_item`
--

DROP TABLE IF EXISTS `find_item`;
CREATE TABLE IF NOT EXISTS `find_item` (
  `id_i` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `i_tombo` bigint(20) NOT NULL DEFAULT 0,
  `i_manitestation` int(11) DEFAULT 0,
  `i_titulo` text COLLATE utf8_bin DEFAULT NULL,
  `i_status` int(11) NOT NULL DEFAULT 0,
  `i_aquisicao` int(11) NOT NULL DEFAULT 0,
  `i_year` int(11) NOT NULL DEFAULT 0,
  `i_localization` text COLLATE utf8_bin DEFAULT NULL,
  `i_ln1` varchar(80) COLLATE utf8_bin DEFAULT NULL,
  `i_ln2` varchar(80) COLLATE utf8_bin DEFAULT NULL,
  `i_ln3` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `i_ln4` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `i_type` int(11) NOT NULL,
  `i_work` int(11) NOT NULL,
  `i_identifier` char(15) COLLATE utf8_bin NOT NULL,
  `i_uri` char(100) COLLATE utf8_bin DEFAULT NULL,
  `i_library` int(11) NOT NULL,
  `i_library_place` int(11) NOT NULL,
  `i_library_classification` int(11) NOT NULL,
  `i_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `i_ip` char(20) COLLATE utf8_bin DEFAULT NULL,
  `i_usuario` int(11) NOT NULL,
  `i_dt_emprestimo` int(11) DEFAULT 0,
  `i_dt_prev` int(11) DEFAULT NULL,
  `i_dt_renovavao` int(11) DEFAULT NULL,
  `i_exemplar` int(11) DEFAULT 1,
  UNIQUE KEY `id_i` (`id_i`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
