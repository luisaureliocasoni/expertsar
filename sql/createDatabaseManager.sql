-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 02-Ago-2017 às 22:32
-- Versão do servidor: 5.6.35
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloudplu_expertsAr46279`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `Licoes`
--

DROP TABLE IF EXISTS `Licoes`;
CREATE TABLE IF NOT EXISTS `Licoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `idMantenedorCriou` int(11) DEFAULT NULL,
  `idMantenedorAlterou` int(11) DEFAULT NULL,
  `textoLicao` longtext,
  PRIMARY KEY (`id`),
  KEY `fki_IdMantenedorQueAlterou` (`idMantenedorAlterou`),
  KEY `idMantenedorQueCriou` (`idMantenedorCriou`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Mantenedores`
--

DROP TABLE IF EXISTS `Mantenedores`;
CREATE TABLE IF NOT EXISTS `Mantenedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `senha` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarioUnico` (`usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Perguntas`
--

DROP TABLE IF EXISTS `Perguntas`;
CREATE TABLE IF NOT EXISTS `Perguntas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `enunciado` longtext NOT NULL,
  `resposta` longtext NOT NULL,
  `idLicao` int(11) NOT NULL,
  `respostaAlgebra` longtext,
  PRIMARY KEY (`id`),
  KEY `fki_idLicaoPertence` (`idLicao`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `Usuarios`
--

DROP TABLE IF EXISTS `Usuarios`;
CREATE TABLE IF NOT EXISTS `Usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(105) NOT NULL,
  `nome` varchar(256) NOT NULL,
  `pass` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `UsuariosLicoes`
--

DROP TABLE IF EXISTS `UsuariosLicoes`;
CREATE TABLE IF NOT EXISTS `UsuariosLicoes` (
  `idUsuario` int(11) NOT NULL,
  `idLicao` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `idLicaoConcluida` (`idLicao`),
  KEY `idUsuarioQueConcluiu` (`idUsuario`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
