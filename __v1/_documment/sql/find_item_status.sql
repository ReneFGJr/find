-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: bdlivre.ufrgs.br
-- Tempo de geração: 29/01/2021 às 12:20
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
-- Estrutura para tabela `find_item_status`
--

CREATE TABLE `find_item_status` (
  `id_is` bigint(20) UNSIGNED NOT NULL,
  `is_name` char(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Despejando dados para a tabela `find_item_status`
--

INSERT INTO `find_item_status` (`id_is`, `is_name`) VALUES
(1, 'Catalogação'),
(2, 'Classificação'),
(3, 'Indexação'),
(4, 'Preparo físico'),
(5, 'Disponível'),
(6, 'Emprestado'),
(7, 'Buscando metadados');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `find_item_status`
--
ALTER TABLE `find_item_status`
  ADD UNIQUE KEY `id_is` (`id_is`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `find_item_status`
--
ALTER TABLE `find_item_status`
  MODIFY `id_is` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
