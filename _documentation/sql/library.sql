-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 02, 2025 at 09:52 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `find`
--

-- --------------------------------------------------------

--
-- Table structure for table `library`
--

DROP TABLE IF EXISTS `library`;
CREATE TABLE IF NOT EXISTS `library` (
  `id_l` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `l_name` text NOT NULL,
  `l_code` char(15) NOT NULL,
  `l_id` int NOT NULL,
  `l_logo` char(80) NOT NULL DEFAULT '',
  `l_about` text NOT NULL,
  `l_visible` int NOT NULL DEFAULT '1',
  `l_net` int NOT NULL DEFAULT '0',
  UNIQUE KEY `id_l` (`id_l`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `library`
--

INSERT INTO `library` (`id_l`, `l_name`, `l_code`, `l_id`, `l_logo`, `l_about`, `l_visible`, `l_net`) VALUES
(1, 'Biblioteca CEDAP 2', '1001', 1001, 'img/logo_library.png', 'A biblioteca do CEDAP é uma biblioteca especializada nas áreas de Biblioteconomia, Arquivologia e Ciência da Informação.', 0, 0),
(2, 'Beabah! - Bibliotecas comunitárias RS', '1003', 1003, 'img/logo/logo-beabah_mini.jpg', '', 1, 0),
(3, 'Books', '1000', 1000, 'img/logo-brapci_livros_mini.png', '', 0, 0),
(4, 'Biblioteca Rene e Viviane', '1002', 1002, 'img/logo_library.png', '', 0, 0),
(5, 'Biblioteca Pedro Cunha', '1004', 1004, 'img/logo/africanamente.jpg', '', 1, 0),
(6, 'Biblioteca test', '1005', 1005, 'img/lib/logo_teste.jpg', '', 1, 0),
(7, 'Livros Biblioteca Infantil', '1009', 1009, 'img/logo-livro.png', '', 0, 0),
(8, 'Biblioteca do Curso de Biblioteconomia EAD/UFRGS', '1011', 1011, 'img/logo/logo_bibead.png', '', 1, 0),
(9, 'Biblioteca Ponto de Cultura O Araçá', '1012', 1012, '', '', 1, 0),
(10, 'Biblioteca InfantoJuvenil da UFR', '1017', 1017, '', '', 1, 0),
(11, 'Biblioteca Misturaí', '1018', 1018, '', '', 1, 0),
(12, 'Biblioteca Sérgio Caparelli - EMEF Saint-Hilaire', '1019', 1019, 'img/logo/logo_1019.jpg', '', 1, 0),
(13, 'Biblioteca Ataîru (Assentamento Belo Monte)', '1013', 1013, 'img/logo/logo_1013.png', '', 1, 0),
(14, 'Biblioteca Sede de Partilha (assentamento Apolônio de Carvalho)', '1014', 1014, 'img/logo/logo_1014.png', '', 1, 0),
(15, 'Biblioteca SOMOS', '1015', 1015, 'img/logo-livro.png', '', 1, 0),
(16, 'Biblioteca Leverdogil de Freitas', '1016', 1016, 'img/logo-livro.png', 'Biblioteca do IPDAE', 1, 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
