-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 18, 2019 at 07:48 AM
-- Server version: 5.7.25-0ubuntu0.16.04.2
-- PHP Version: 7.0.33-2+ubuntu16.04.1+deb.sury.org+2+will+reach+end+of+life+in+april+2019

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `propel`
--

-- --------------------------------------------------------

--
-- Table structure for table `itens_historico`
--

CREATE TABLE `itens_historico` (
  `id_ih` bigint(20) UNSIGNED NOT NULL,
  `ih_tombo` varchar(14) NOT NULL,
  `ih_type` int(11) NOT NULL,
  `ih_user` int(11) NOT NULL,
  `ih_log` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `itens_historico`
--

INSERT INTO `itens_historico` (`id_ih`, `ih_tombo`, `ih_type`, `ih_user`, `ih_log`) VALUES
(1, '100100005643', 3, 0, 1),
(2, '100100005650', 3, 0, 1),
(3, '100100005629', 3, 0, 1),
(4, '100100005681', 3, 0, 1),
(5, '100100005636', 3, 0, 1),
(6, '100100005780', 3, 0, 1),
(7, '100100005704', 3, 0, 1),
(8, '100100005612', 3, 0, 1),
(9, '100100005667', 3, 0, 1),
(10, '100100000105', 3, 0, 1),
(11, '100100005773', 3, 0, 1),
(12, '100100005766', 3, 0, 1),
(13, '100100005742', 3, 0, 1),
(14, '100100000914', 3, 0, 1),
(15, '100100002178', 3, 0, 1),
(16, '100100002017', 3, 0, 1),
(17, '100100002055', 3, 0, 1),
(18, '100100000518', 3, 0, 1),
(19, '100100000525', 3, 0, 1),
(20, '100100000532', 3, 0, 1),
(21, '100100000549', 3, 0, 1),
(22, '100100000617', 3, 0, 1),
(23, '100100000631', 3, 0, 1),
(24, '100100001171', 3, 0, 1),
(25, '100100001119', 3, 0, 1),
(26, '100100000709', 3, 0, 1),
(27, '100100001003', 3, 0, 1),
(28, '100100000792', 3, 0, 1),
(29, '100100000808', 3, 0, 1),
(30, '100100000112', 3, 0, 1),
(31, '100100002093', 3, 0, 1),
(32, '100100002154', 3, 0, 1),
(33, '100100002079', 3, 0, 1),
(34, '100100000372', 3, 0, 1),
(35, '100100000365', 3, 0, 1),
(36, '100100000358', 3, 0, 1),
(37, '100100000389', 3, 0, 1),
(38, '100100000396', 3, 0, 1),
(39, '100100000488', 3, 0, 1),
(40, '100100000242', 3, 0, 1),
(41, '100100000235', 3, 0, 1),
(42, '100100000952', 3, 0, 1),
(43, '100100002130', 3, 0, 1),
(44, '100100005919', 3, 0, 1),
(45, '100100000990', 3, 0, 1),
(46, '100100000211', 3, 0, 1),
(47, '100100000198', 3, 0, 1),
(48, '100100000181', 3, 0, 1),
(49, '100100000174', 3, 0, 1),
(50, '100100000259', 3, 0, 1),
(51, '100100000280', 3, 0, 1),
(52, '100100000266', 3, 0, 1),
(53, '100100001041', 3, 0, 1),
(54, '100100000624', 3, 0, 1),
(55, '100100005841', 3, 0, 1),
(56, '100100000310', 3, 0, 1),
(57, '100100000136', 3, 0, 1),
(58, '100100000136', 3, 0, 1),
(59, '100100000136', 3, 0, 1),
(60, '100100002116', 3, 0, 1),
(61, '100100000167', 3, 0, 1),
(62, '100100000228', 3, 0, 1),
(63, '100100001089', 3, 0, 1),
(64, '100100000730', 3, 0, 1),
(65, '100100000341', 3, 0, 1),
(66, '100100005971', 3, 0, 1),
(67, '100100000273', 3, 0, 1),
(68, '100100005872', 3, 0, 1),
(69, '100100000297', 3, 0, 1),
(70, '100100000013', 3, 0, 1),
(71, '100100000204', 3, 0, 1),
(72, '100100000594', 3, 0, 1),
(73, '100100000907', 3, 0, 1),
(74, '100100000938', 3, 0, 1),
(75, '100100001201', 3, 0, 1),
(76, '100100001102', 3, 0, 1),
(77, '100100001102', 3, 0, 1),
(78, '100100000945', 3, 0, 1),
(79, '100100000143', 3, 0, 1),
(80, '100100000501', 3, 0, 1),
(81, '100100002192', 3, 0, 1),
(82, '100100001157', 3, 0, 1),
(83, '100100001102', 1, 0, 1),
(84, '100100001102', 4, 0, 1),
(85, '100100000136', 4, 0, 1),
(86, '100100000600', 3, 0, 1),
(87, '100100000600', 4, 0, 1),
(88, '100100000648', 4, 0, 1),
(89, '100100000402', 3, 0, 1),
(90, '100100000662', 3, 0, 1),
(91, '100100000334', 3, 0, 1),
(92, '100100000327', 3, 0, 1),
(93, '100100000778', 3, 0, 1),
(94, '100100000419', 3, 0, 1),
(95, '100100000983', 3, 0, 1),
(96, '100100000976', 3, 0, 1),
(97, '100100000679', 3, 0, 1),
(98, '100100000761', 3, 0, 1),
(99, '100100000754', 3, 0, 1),
(100, '100100000747', 3, 0, 1),
(101, '100100002826', 3, 0, 1),
(102, '100100002864', 3, 0, 1),
(103, '100100002888', 3, 0, 1),
(104, '100100002901', 3, 0, 1),
(105, '100100002949', 3, 0, 1),
(106, '100100002840', 3, 0, 1),
(107, '100100002963', 3, 0, 1),
(108, '100100002987', 3, 0, 1),
(109, '100100001140', 3, 0, 1),
(110, '100100000815', 3, 0, 1),
(111, '100100000068', 3, 0, 1),
(112, '100100002031', 3, 0, 1),
(113, '100100000037', 3, 0, 1),
(114, '100100000044', 3, 0, 1),
(115, '100100000020', 3, 0, 1),
(116, '100100000051', 3, 0, 1),
(117, '100100003182', 3, 0, 1),
(118, '100100000082', 3, 0, 1),
(119, '100100000099', 3, 0, 1),
(120, '100100000129', 3, 0, 1),
(121, '100100000150', 3, 0, 1),
(122, '100100000303', 3, 0, 1),
(123, '100100000648', 3, 0, 1),
(124, '100100000686', 3, 0, 1),
(125, '100100000716', 3, 0, 1),
(126, '100100000921', 3, 0, 1),
(127, '100100001010', 3, 0, 1),
(128, '100100002215', 3, 0, 1),
(129, '100100003588', 3, 0, 1),
(130, '100100003601', 3, 0, 1),
(131, '100100005889', 3, 0, 1),
(132, '100100005865', 3, 0, 1),
(133, '100100005858', 3, 0, 1),
(134, '100100005759', 3, 0, 1),
(135, '100100005230', 3, 0, 1),
(136, '100100005216', 3, 0, 1),
(137, '100100003199', 3, 0, 1),
(138, '100100003052', 3, 0, 1),
(139, '100100003137', 3, 0, 1),
(140, '100100003144', 3, 0, 1),
(141, '100100003069', 3, 0, 1),
(142, '100100002277', 3, 0, 1),
(143, '100100002239', 3, 0, 1),
(144, '100100000723', 3, 0, 1),
(145, '100100002925', 3, 0, 1),
(146, '100100002253', 3, 0, 1),
(147, '100100002918', 3, 0, 1),
(148, '100100003083', 3, 0, 1),
(149, '100100003151', 3, 0, 1),
(150, '100100003076', 3, 0, 1),
(151, '100100002956', 3, 0, 1),
(152, '100100002970', 3, 0, 1),
(153, '100100003038', 3, 0, 1),
(154, '100100002857', 3, 0, 1),
(155, '100100002871', 3, 0, 1),
(156, '100100002291', 3, 0, 1),
(157, '100100002895', 3, 0, 1),
(158, '100100002994', 3, 0, 1),
(159, '100100002314', 3, 0, 1),
(160, '100100003168', 3, 0, 1),
(161, '100100003113', 3, 0, 1),
(162, '100100003205', 3, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `itens_historico`
--
ALTER TABLE `itens_historico`
  ADD UNIQUE KEY `id_ih` (`id_ih`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `itens_historico`
--
ALTER TABLE `itens_historico`
  MODIFY `id_ih` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;