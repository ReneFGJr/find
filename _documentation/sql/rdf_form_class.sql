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
-- Estrutura para tabela `rdf_form_class`
--

CREATE TABLE `rdf_form_class` (
  `id_sc` bigint(20) UNSIGNED NOT NULL,
  `sc_class` int(11) NOT NULL,
  `sc_propriety` int(11) NOT NULL,
  `sc_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sc_range` int(11) NOT NULL,
  `sc_ativo` int(11) NOT NULL DEFAULT '1',
  `sc_ord` int(11) NOT NULL DEFAULT '99',
  `sc_library` int(11) NOT NULL DEFAULT '0',
  `sc_global` int(11) NOT NULL DEFAULT '0',
  `sc_group` char(40) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `rdf_form_class`
--
ALTER TABLE `rdf_form_class`
  ADD UNIQUE KEY `id_sc` (`id_sc`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `rdf_form_class`
--
ALTER TABLE `rdf_form_class`
  MODIFY `id_sc` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
