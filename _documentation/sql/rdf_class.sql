-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: bdlivre.ufrgs.br
-- Tempo de geração: 29/03/2022 às 10:06
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
-- Estrutura para tabela `rdf_class`
--

CREATE TABLE `rdf_class` (
  `id_c` Serial NOT NULL,
  `c_class` varchar(200) NOT NULL,
  `c_equivalent` int(11) NOT NULL DEFAULT '0',
  `c_prefix` int(11) NOT NULL,
  `c_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `c_class_main` int(11) NOT NULL DEFAULT '0',
  `c_type` char(1) NOT NULL,
  `c_order` int(11) NOT NULL DEFAULT '99',
  `c_pa` int(11) NOT NULL DEFAULT '0',
  `c_repetitive` int(11) NOT NULL DEFAULT '1',
  `c_vc` int(11) NOT NULL DEFAULT '0',
  `c_find` int(11) NOT NULL DEFAULT '0',
  `c_identify` int(11) NOT NULL DEFAULT '0',
  `c_contextualize` int(11) NOT NULL DEFAULT '0',
  `c_justify` int(11) NOT NULL DEFAULT '0',
  `c_url` char(100) NOT NULL,
  `c_url_update` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `rdf_class`
--
ALTER TABLE `rdf_class`
  ADD UNIQUE KEY `id_c` (`id_c`),
  ADD UNIQUE KEY `classes` (`c_class`(30));

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `rdf_class`
--
ALTER TABLE `rdf_class`
  MODIFY `id_c` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
