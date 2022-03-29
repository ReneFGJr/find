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
-- Estrutura para tabela `rdf_concept`
--

CREATE TABLE `rdf_concept` (
  `id_cc` bigint(20) UNSIGNED NOT NULL,
  `cc_class` int(11) NOT NULL,
  `cc_use` int(11) NOT NULL DEFAULT '0',
  `cc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cc_pref_term` int(11) NOT NULL,
  `cc_origin` char(20) NOT NULL,
  `cc_update` date NOT NULL,
  `cc_status` int(11) NOT NULL DEFAULT '0',
  `cc_library` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `rdf_concept`
--
ALTER TABLE `rdf_concept`
  ADD UNIQUE KEY `id_cc` (`id_cc`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `rdf_concept`
--
ALTER TABLE `rdf_concept`
  MODIFY `id_cc` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
