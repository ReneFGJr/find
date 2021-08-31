-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 29/01/2021 às 15:05
-- Versão do servidor: 5.7.20
-- Versão do PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `library`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `find_item_historic`
--

CREATE TABLE `find_item_historic` (
  `id_h` bigint(20) UNSIGNED NOT NULL,
  `h_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `h_item` int(11) NOT NULL DEFAULT '0',
  `h_status` int(11) NOT NULL DEFAULT '0',
  `h_ip` char(20) NOT NULL,
  `h_user` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `find_item_historic`
--
ALTER TABLE `find_item_historic`
  ADD UNIQUE KEY `id_h` (`id_h`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `find_item_historic`
--
ALTER TABLE `find_item_historic`
  MODIFY `id_h` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
