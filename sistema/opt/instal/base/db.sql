-- --------------------------------------------------------
-- Servidor:                     localhost
-- Versão do servidor:           5.6.24 - MySQL Community Server (GPL)
-- OS do Servidor:               Win32
-- HeidiSQL Versão:              9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura para tabela lliure_border_collie.ll_lliure_apps
CREATE TABLE IF NOT EXISTS `ll_lliure_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) NOT NULL,
  `pasta` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pasta` (`pasta`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela lliure_border_collie.ll_lliure_apps: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `ll_lliure_apps` DISABLE KEYS */;
/*!40000 ALTER TABLE `ll_lliure_apps` ENABLE KEYS */;


-- Copiando estrutura para tabela lliure_border_collie.ll_lliure_su
CREATE TABLE IF NOT EXISTS `ll_lliure_su` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(200) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `nome` varchar(200) NOT NULL,
  `email` varchar(256) NOT NULL,
  `foto` varchar(256) DEFAULT NULL,
  `grupo` varchar(10) NOT NULL DEFAULT 'user',
  `themer` varchar(50) DEFAULT 'default',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Copiando dados para a tabela lliure_border_collie.ll_lliure_su: ~0 rows (aproximadamente)
/*!40000 ALTER TABLE `ll_lliure_su` DISABLE KEYS */;
INSERT INTO `ll_lliure_su` (`id`, `login`, `senha`, `nome`, `email`, `foto`, `grupo`, `themer`) VALUES
	(1, 'dev', 'aa7da2ab1f526a118c22fbff233ace2d', '', '', NULL, 'dev', 'default');
/*!40000 ALTER TABLE `ll_lliure_su` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
