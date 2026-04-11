-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 11-Abr-2026 às 15:24
-- Versão do servidor: 10.1.32-MariaDB
-- PHP Version: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `controle_contas`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `contas`
--

CREATE TABLE `contas` (
  `serial` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `pago` char(1) NOT NULL,
  `vencimento` int(11) NOT NULL,
  `caminho` varchar(255) DEFAULT NULL,
  `ativo` char(1) NOT NULL,
  `mes` char(2) DEFAULT NULL,
  `ano` char(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `contas`
--

INSERT INTO `contas` (`serial`, `nome`, `pago`, `vencimento`, `caminho`, `ativo`, `mes`, `ano`) VALUES
(10, 'UNIMED BELEM', '', 10, NULL, '1', '10', '2025'),
(11, 'internet', '', 15, NULL, '1', '10', '2025'),
(12, 'condominio taguara', '', 20, NULL, '1', '10', '2025'),
(13, 'cartao magalu', '', 9, NULL, '1', '10', '2025'),
(14, 'loja sonho dos pes', '', 10, NULL, '1', '10', '2025'),
(15, 'Condominio Catavento ', '', 10, NULL, '1', '10', '2025'),
(16, 'Moto Rosana', '', 12, NULL, '1', '10', '2025'),
(17, 'CartÃ£o Riachuelo', '', 13, NULL, '1', '10', '2025'),
(18, 'Moto Pedro ', '', 13, NULL, '1', '10', '2025'),
(19, 'Apartamento Catavento ', '', 14, NULL, '1', '10', '2025'),
(20, 'Loja Renner', '', 15, NULL, '1', '10', '2025'),
(21, 'Novo Mundo', '', 20, NULL, '1', '10', '2025'),
(22, 'TV a Cabo', '', 20, NULL, '1', '10', '2025'),
(23, 'CartÃ£o Master Pai ', '', 25, NULL, '1', '10', '2025'),
(24, 'studio z', '', 11, NULL, '1', '10', '2025'),
(41, 'cartao magalu', '', 9, NULL, '1', '11', '2025'),
(42, 'UNIMED BELEM', '', 10, NULL, '1', '11', '2025'),
(43, 'loja sonho dos pes', '', 10, NULL, '1', '11', '2025'),
(44, 'Condominio Catavento ', '', 10, NULL, '1', '11', '2025'),
(45, 'studio z', '', 11, NULL, '1', '11', '2025'),
(46, 'Moto Rosana', '', 12, NULL, '1', '11', '2025'),
(47, 'CartÃ£o Riachuelo', '', 13, NULL, '1', '11', '2025'),
(48, 'Moto Pedro ', '', 13, NULL, '1', '11', '2025'),
(49, 'Apartamento Catavento ', '', 14, NULL, '1', '11', '2025'),
(50, 'internet', '', 15, NULL, '1', '11', '2025'),
(51, 'Loja Renner', '', 15, NULL, '1', '11', '2025'),
(52, 'condominio taguara', '', 20, NULL, '1', '11', '2025'),
(53, 'Novo Mundo', '', 20, NULL, '1', '11', '2025'),
(54, 'TV a Cabo', '', 20, NULL, '1', '11', '2025'),
(55, 'CartÃ£o Master Pai ', '', 25, NULL, '1', '11', '2025');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contas`
--
ALTER TABLE `contas`
  ADD PRIMARY KEY (`serial`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contas`
--
ALTER TABLE `contas`
  MODIFY `serial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
