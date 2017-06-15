-- ----------------------------------------------------------------------------
-- MySQL Workbench Migration
-- Migrated Schemata: algebra
-- Source Schemata: algebra
-- Created: Thu May 11 16:44:21 2017
-- Workbench Version: 6.3.8
-- ----------------------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;


-- ----------------------------------------------------------------------------
-- Table algebra.Mantenedores
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Mantenedores` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(50) NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `senha` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `usuarioUnico` (`usuario` ASC));


-- ----------------------------------------------------------------------------
-- Table algebra.Usuarios
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(105) NOT NULL,
  `nome` VARCHAR(256) NOT NULL,
  `pass` VARCHAR(60) NOT NULL,
  PRIMARY KEY (`id`));


-- ----------------------------------------------------------------------------
-- Table algebra.Licoes
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Licoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `idMantenedorCriou` INT NULL,
  `idMantenedorAlterou` INT NULL,
  `textoLicao` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fki_IdMantenedorQueAlterou` (`idMantenedorAlterou` ASC),
  CONSTRAINT `IdMantenedorQueAlterou`
    FOREIGN KEY (`idMantenedorAlterou`)
    REFERENCES `Mantenedores` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `idMantenedorQueCriou`
    FOREIGN KEY (`idMantenedorCriou`)
    REFERENCES `Mantenedores` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE);
	
-- ----------------------------------------------------------------------------
-- Table algebra.Perguntas
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `Perguntas` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `enunciado` LONGTEXT NOT NULL,
  `resposta` LONGTEXT NOT NULL,
  `idLicao` INT NOT NULL,
  `respostaAlgebra` LONGTEXT NULL,
  PRIMARY KEY (`id`),
  INDEX `fki_idLicaoPertence` (`idLicao` ASC),
  CONSTRAINT `idLicaoPertence`
    FOREIGN KEY (`idLicao`)
    REFERENCES `Licoes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);

-- ----------------------------------------------------------------------------
-- Table algebra.UsuariosLicoes
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `UsuariosLicoes` (
  `idUsuario` INT NOT NULL,
  `idLicao` INT NOT NULL,
  `id` INT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  CONSTRAINT `idLicaoConcluida`
    FOREIGN KEY (`idLicao`)
    REFERENCES `Licoes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `idUsuarioQueConcluiu`
    FOREIGN KEY (`idUsuario`)
    REFERENCES `Usuarios` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
SET FOREIGN_KEY_CHECKS = 1;
