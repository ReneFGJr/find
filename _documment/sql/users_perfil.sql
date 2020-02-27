-- phpMyAdmin SQL Dump
-- version 5.0.0-alpha1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 19-Fev-2020 às 13:24
-- Versão do servidor: 8.0.19-0ubuntu0.19.10.3
-- versão do PHP: 7.3.11-0ubuntu0.19.10.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `viaoro`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `users_perfil`
--

CREATE TABLE `users_perfil` (
  `id_pe` bigint UNSIGNED NOT NULL,
  `pe_abrev` char(4) NOT NULL,
  `pe_descricao` char(100) NOT NULL,
  `pe_nivel` int NOT NULL DEFAULT '9'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Extraindo dados da tabela `users_perfil`
--

INSERT INTO `users_perfil` (`id_pe`, `pe_abrev`, `pe_descricao`, `pe_nivel`) VALUES
(1, '#ADM', 'Administrador do Sistema', 9);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `users_perfil`
--
ALTER TABLE `users_perfil`
  ADD UNIQUE KEY `id_pe` (`id_pe`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `users_perfil`
--
ALTER TABLE `users_perfil`
  MODIFY `id_pe` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

