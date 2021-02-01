-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 01/02/2021 às 11:03
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
-- Estrutura para tabela `users_add`
--

CREATE TABLE `users_add` (
  `id_ua` bigint(20) UNSIGNED NOT NULL,
  `ua_us` int(11) NOT NULL,
  `ua_nasc` date NOT NULL DEFAULT '1900-01-01',
  `us_genero` int(11) NOT NULL,
  `us_cep` char(10) NOT NULL,
  `us_logradouro` char(100) NOT NULL,
  `ua_number` char(15) NOT NULL,
  `us_complemento` char(50) NOT NULL,
  `us_bairro` char(50) NOT NULL,
  `us_localidade` char(50) NOT NULL,
  `us_uf` char(2) NOT NULL,
  `us_ibge` int(11) NOT NULL DEFAULT '0',
  `us_gia` char(15) NOT NULL,
  `us_ddd` char(2) NOT NULL,
  `us_siafi` char(5) NOT NULL,
  `us_raca` int(11) NOT NULL,
  `us_escolaridade` int(11) NOT NULL,
  `us_escolaridade_st` int(11) NOT NULL DEFAULT '0',
  `us_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `us_update` date NOT NULL DEFAULT '1900-01-01'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `users_add`
--

INSERT INTO `users_add` (`id_ua`, `ua_us`, `ua_nasc`, `us_genero`, `us_cep`, `us_logradouro`, `ua_number`, `us_complemento`, `us_bairro`, `us_localidade`, `us_uf`, `us_ibge`, `us_gia`, `us_ddd`, `us_siafi`, `us_raca`, `us_escolaridade`, `us_escolaridade_st`, `us_created`, `us_update`) VALUES
(1, 2, '1900-01-01', 0, '90660-900', 'Avenida Bento Gonçalves 1515', '1515', 'Ap 1707D', 'Santo Antônio', 'Porto Alegre', 'RS', 4314902, '51', '', '8801', 0, 0, 0, '2021-02-01 04:23:56', '1900-01-01'),
(2, 12, '1900-01-01', 0, '', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, '2021-02-01 05:14:59', '1900-01-01'),
(3, 13, '1900-01-01', 3, '90650-002', 'Avenida Bento Gonçalves', '1515', 'Ap 1707D', 'Partenon', 'Porto Alegre', 'RS', 4314902, '51', '', '8801', 3, 10, 3, '2021-02-01 10:16:19', '1900-01-01'),
(4, 7, '1900-01-01', 0, '', '', '', '', '', '', '', 0, '', '', '', 0, 0, 0, '2021-02-01 10:44:55', '1900-01-01');

--
-- Índices de tabelas apagadas
--

--
-- Índices de tabela `users_add`
--
ALTER TABLE `users_add`
  ADD UNIQUE KEY `id_ua` (`id_ua`);

--
-- AUTO_INCREMENT de tabelas apagadas
--

--
-- AUTO_INCREMENT de tabela `users_add`
--
ALTER TABLE `users_add`
  MODIFY `id_ua` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
