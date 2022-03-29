-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: bdlivre.ufrgs.br
-- Tempo de geração: 29/03/2022 às 10:16
-- Versão do servidor: 5.5.31
-- Versão do PHP: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
-- Estrutura para tabela `rdf_data`
--

CREATE TABLE `rdf_data` (
  `id_d` bigint(20) UNSIGNED NOT NULL,
  `d_r1` int(11) NOT NULL,
  `d_p` int(11) NOT NULL,
  `d_r2` int(11) NOT NULL,
  `d_literal` int(11) NOT NULL DEFAULT '0',
  `d_creadted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `d_update` int(11) NOT NULL DEFAULT '0',
  `d_library` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `rdf_data`
--
ALTER TABLE `rdf_data`
  ADD UNIQUE KEY `id_d` (`id_d`),
  ADD KEY `data1` (`d_r1`),
  ADD KEY `data2` (`d_r2`),
  ADD KEY `data3` (`d_p`),
  ADD KEY `data4` (`d_r1`,`d_r2`,`d_p`,`d_literal`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `rdf_data`
--
ALTER TABLE `rdf_data`
  MODIFY `id_d` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
