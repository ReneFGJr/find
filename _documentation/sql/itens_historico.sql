-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 16-Jun-2022 às 18:58
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
-- Estrutura da tabela `itens_historico`
--

DROP TABLE IF EXISTS `itens_historico`;
CREATE TABLE IF NOT EXISTS `itens_historico` (
  `id_jh` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ih_code` int(11) NOT NULL,
  `ih_datetime` timestamp NOT NULL,
  `ih_user` int(11) NOT NULL,
  `ih_tombo` int(11) NOT NULL,
  `ih_library` int(11) NOT NULL,
  UNIQUE KEY `id_jh` (`id_jh`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `itens_historico`
--

INSERT INTO `itens_historico` (`id_jh`, `ih_code`, `ih_datetime`, `ih_user`, `ih_tombo`, `ih_library`) VALUES
(1, 701, '2022-06-16 16:57:43', 1, 2642, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
