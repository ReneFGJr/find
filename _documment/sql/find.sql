-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 03, 2017 at 02:58 PM
-- Server version: 5.6.20-log
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `find`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('0hj9lk1pkqii8q0kkr8i5rd2kf6bla1p', '::1', 1509675416, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637353135343b69647c693a313b),
('1jib2nb6onle4a70eb262tktq5ujg712', '::1', 1509717782, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393731373738323b69647c693a313b),
('34en2d01lis4vthj947us4k31jv6563g', '::1', 1509676382, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637363130313b69647c693a313b),
('4cgvlfvsk3pj5dh59e2csp5mns9vtau9', '::1', 1509675102, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637343834363b69647c693a313b),
('8i0vk98i8ahc6qvh22mn3orh4sqr52b4', '::1', 1509668971, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393636383731343b69647c693a313b),
('aamnnjpbq810g9oafltr0qfrg7rmuess', '::1', 1509668081, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393636373830383b69647c693a313b),
('ao36alijgisks154m12dg90dieg3fmpj', '::1', 1509674839, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637343534353b69647c693a313b),
('ddaepkervdn1bl0af77abddnaehcpds2', '::1', 1509678463, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637383235363b69647c693a313b),
('dqekpi54nmn3sfh16al4ld2r173g7c72', '::1', 1509718735, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393731383433373b69647c693a313b),
('dtfcecu2jtj3n3dqcden6noa2lqrb7t0', '::1', 1509677807, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637373531343b69647c693a313b),
('e7pkfu5mc51vptd8v1htq9aqoffs8s8n', '::1', 1509675968, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637353736313b69647c693a313b),
('eqdekqin0aqibqtnli0d0nbhqbr7ed2l', '::1', 1509669292, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393636393031363b69647c693a313b),
('fuap470a6j4nfdm203qukbinvvr2tjre', '::1', 1509720317, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393732303231303b69647c693a313b),
('gnh7520n04i50p3ls0eraj69k069qp2s', '::1', 1509669698, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393636393430343b69647c693a313b),
('hd0gbh3v3hti1jb7c0l2i407bhf56iok', '::1', 1509669958, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393636393734323b69647c693a313b),
('hlqhpfndp8oa4e365pno49bu10sune6m', '::1', 1509677176, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637363838303b69647c693a313b),
('htp5a28o11485h2fnh0r1o4vbj1mtjuq', '::1', 1509673679, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637333430343b69647c693a313b),
('j26dehdd27ofjvq8ng98mh3hk5ppof1k', '::1', 1509677495, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637373139383b69647c693a313b),
('k4dsqgv7r8f0vvjnrlfhpi2h21r4vbf8', '::1', 1509666698, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393636363639383b69647c693a313b),
('m3s91hifv1p44chrqg17k7jeucihagpo', '::1', 1509670677, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637303338363b69647c693a313b),
('meb29hm1n93ouoentgtdatm8bnruu1ot', '::1', 1509670970, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637303639303b69647c693a313b),
('ni8kdl9el0scfllv0u54src56q1pg610', '::1', 1509676799, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637363535353b69647c693a313b),
('ntagg55tmefemnre1eiq0l2v3ilaos07', '::1', 1509670371, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637303037343b69647c693a313b),
('ouqna37bjb8s6bjh3dliv60pmnbuqjah', '::1', 1509719235, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393731393037333b69647c693a313b),
('r0c1c8hik91iffj2a1qsp9jfejikh3d4', '::1', 1509678121, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637373832343b69647c693a313b),
('rk5aibgruhtffhgalm9a5oul5u2b04vu', '::1', 1509671206, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637313031393b69647c693a313b),
('u9b6tggkebntf86i3mtstqqpmbgfbanu', '::1', 1509675745, 0x5f5f63695f6c6173745f726567656e65726174657c693a313530393637353435353b69647c693a313b);

-- --------------------------------------------------------

--
-- Table structure for table `rdf_class`
--

CREATE TABLE IF NOT EXISTS `rdf_class` (
`id_c` bigint(20) unsigned NOT NULL,
  `c_class` varchar(200) NOT NULL,
  `c_prefix` int(11) NOT NULL,
  `c_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `c_class_main` int(11) NOT NULL DEFAULT '0',
  `c_type` char(1) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `rdf_class`
--

INSERT INTO `rdf_class` (`id_c`, `c_class`, `c_prefix`, `c_created`, `c_class_main`, `c_type`) VALUES
(1, 'Agent', 0, '2017-11-03 14:33:53', 0, 'C'),
(2, 'Person', 0, '2017-11-03 14:33:53', 1, 'C'),
(3, 'Family', 0, '2017-11-03 14:34:34', 1, 'C'),
(4, 'Corporate Body', 0, '2017-11-03 14:34:34', 1, 'C'),
(5, 'prefLabel', 4, '2017-11-03 14:51:55', 0, 'P'),
(6, 'altLabel', 4, '2017-11-03 14:52:07', 0, 'P');

-- --------------------------------------------------------

--
-- Table structure for table `rdf_concept`
--

CREATE TABLE IF NOT EXISTS `rdf_concept` (
`id_c` bigint(20) unsigned NOT NULL,
  `c_class` int(11) NOT NULL,
  `c_use` int(11) NOT NULL DEFAULT '0',
  `c_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `c_pref_term` int(11) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `rdf_concept`
--

INSERT INTO `rdf_concept` (`id_c`, `c_class`, `c_use`, `c_created`, `c_pref_term`) VALUES
(1, 2, 0, '2017-11-03 14:44:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rdf_name`
--

CREATE TABLE IF NOT EXISTS `rdf_name` (
`id_n` bigint(20) unsigned NOT NULL,
  `n_name` varchar(250) NOT NULL,
  `n_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rdf_name`
--

INSERT INTO `rdf_name` (`id_n`, `n_name`, `n_created`) VALUES
(1, 'Bufrem, Leilah Santiago', '2017-11-03 14:18:55'),
(2, 'Santiago Bufrem, Leilah', '2017-11-03 14:26:45'),
(3, 'Leilah Santiago Bufrem', '2017-11-03 14:26:45');

-- --------------------------------------------------------

--
-- Table structure for table `rdf_prefix`
--

CREATE TABLE IF NOT EXISTS `rdf_prefix` (
`id_prefix` bigint(20) unsigned NOT NULL,
  `prefix_ref` char(30) NOT NULL,
  `prefix_url` char(250) NOT NULL,
  `prefix_ativo` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `rdf_prefix`
--

INSERT INTO `rdf_prefix` (`id_prefix`, `prefix_ref`, `prefix_url`, `prefix_ativo`) VALUES
(1, 'dc', 'http://purl.org/dc/elements/1.1/', 1),
(2, 'brapci', 'http://basessibi.c3sl.ufpr.br/brapci/index.php/rdf/', 1),
(3, 'rdfs', 'http://www.w3.org/2000/01/rdf-schema', 1),
(4, 'skos', 'http://www.w3.org/2004/02/skos/core', 1),
(5, 'dcterm', 'http://purl.org/dc/terms/', 1),
(6, 'fb', 'http://rdf.freebases.com/ns', 1),
(7, 'gn', 'http://www.geonames.org/ontology#', 1),
(8, 'geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#', 1),
(9, 'lotico', 'http://www.lotico.com/ontology/', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
 ADD PRIMARY KEY (`id`), ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Indexes for table `rdf_class`
--
ALTER TABLE `rdf_class`
 ADD UNIQUE KEY `id_c` (`id_c`);

--
-- Indexes for table `rdf_concept`
--
ALTER TABLE `rdf_concept`
 ADD UNIQUE KEY `id_c` (`id_c`);

--
-- Indexes for table `rdf_name`
--
ALTER TABLE `rdf_name`
 ADD UNIQUE KEY `id_n` (`id_n`);

--
-- Indexes for table `rdf_prefix`
--
ALTER TABLE `rdf_prefix`
 ADD UNIQUE KEY `id_prefix` (`id_prefix`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `rdf_class`
--
ALTER TABLE `rdf_class`
MODIFY `id_c` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `rdf_concept`
--
ALTER TABLE `rdf_concept`
MODIFY `id_c` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `rdf_name`
--
ALTER TABLE `rdf_name`
MODIFY `id_n` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `rdf_prefix`
--
ALTER TABLE `rdf_prefix`
MODIFY `id_prefix` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
