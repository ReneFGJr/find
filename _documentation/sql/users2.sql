-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 12-Mar-2022 às 12:45
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
-- Banco de dados: `brapci`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `users2`
--

DROP TABLE IF EXISTS `users2`;
CREATE TABLE IF NOT EXISTS `users2` (
  `id_us` int(11) NOT NULL AUTO_INCREMENT,
  `us_nome` varchar(100) NOT NULL,
  `us_email` varchar(100) NOT NULL,
  `us_image` varchar(100) NOT NULL,
  `us_genero` varchar(1) NOT NULL,
  `us_verificado` int(11) NOT NULL DEFAULT '0',
  `us_rdf` int(11) NOT NULL DEFAULT '0',
  `us_login` varchar(100) NOT NULL,
  `us_password` varchar(50) NOT NULL,
  `us_password_method` varchar(3) NOT NULL,
  `us_oauth2` varchar(20) NOT NULL,
  `us_lastaccess` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_us`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `users2`
--

INSERT INTO `users2` (`id_us`, `us_nome`, `us_email`, `us_image`, `us_genero`, `us_verificado`, `us_rdf`, `us_login`, `us_password`, `us_password_method`, `us_oauth2`, `us_lastaccess`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '', '', 1, 0, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'MD5', '', 0, '2022-03-02 12:57:52', '2022-03-02 12:57:52');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
