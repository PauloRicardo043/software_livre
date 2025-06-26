-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22/06/2025 às 01:35
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `idea_service_db`
--

DELIMITER $$
--
-- Procedimentos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `expirar_orcamentos_antigos` ()   BEGIN
    UPDATE orcamentos 
    SET status = 'expirado', 
        data_expiracao = CURDATE()
    WHERE status = 'pendente' 
    AND data_solicitacao < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    AND data_expiracao IS NULL;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `data_cadastro` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `nome`, `email`, `telefone`, `endereco`, `cidade`, `estado`, `cep`, `data_cadastro`) VALUES
(1, 'Paulo Ricardo Costa Beber', 'pauloricardocostabeber@gmail.com', '55996969580', 'Mont Serrat 44', 'Panambi', 'RS', '98280000', '2025-06-04'),
(6, 'Glauco', 'glauco.2022005150@aluno.iffar.edu.br', '055999310535', 'Rua Chapada', 'Panambi', 'RS', '98280000', '2025-06-10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contatos`
--

CREATE TABLE `contatos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `setor` enum('tecnico','financeiro','assistencia') NOT NULL,
  `mensagem` text NOT NULL,
  `resposta` text DEFAULT NULL,
  `status` enum('novo','lido','respondido') NOT NULL DEFAULT 'novo',
  `data_envio` date NOT NULL DEFAULT curdate(),
  `data_resposta` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `contatos`
--

INSERT INTO `contatos` (`id`, `cliente_id`, `nome`, `email`, `telefone`, `setor`, `mensagem`, `resposta`, `status`, `data_envio`, `data_resposta`) VALUES
(1, NULL, 'Paulo', 'teste@teste.com', '55999999999', 'financeiro', 'estou devendo tudo', 'olá, pois pague', 'respondido', '2025-06-04', '2025-06-10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `descricao` text NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `navegador` text DEFAULT NULL,
  `data_hora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `tipo`, `descricao`, `usuario_id`, `acao`, `ip`, `navegador`, `data_hora`) VALUES
(1, 'logout', 'Logout realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 20:46:03'),
(2, 'login', 'Login realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 20:46:13'),
(3, 'logout', 'Logout realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:14:17'),
(4, 'login_falha', 'Tentativa de login falhou para o usuário: pauloricardo', NULL, '', '::1', NULL, '2025-06-09 21:14:45'),
(5, 'login_falha', 'Tentativa de login falhou para o usuário: glaucoschneider', NULL, '', '::1', NULL, '2025-06-09 21:14:53'),
(6, 'login', 'Login realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:15:00'),
(7, 'logout', 'Logout realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:15:39'),
(8, 'login', 'Login realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:18:35'),
(9, 'orcamento_excluido', 'Orçamento #7 excluído com sucesso', 10, '', '::1', NULL, '2025-06-09 21:24:42'),
(10, 'logout', 'Logout realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:24:49'),
(11, 'login', 'Login realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:25:08'),
(12, 'logout', 'Logout realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:27:16'),
(13, 'login', 'Login realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 21:27:32'),
(14, 'logout', 'Logout realizado com sucesso', 10, '', '::1', NULL, '2025-06-09 22:00:46'),
(15, 'login_falha', 'Tentativa de login falhou para o usuário: pauloricardo', NULL, '', '::1', NULL, '2025-06-09 22:00:54'),
(16, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-09 22:25:38'),
(17, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-09 22:25:56'),
(18, 'cadastro_cliente', 'Cliente #2 cadastrado', 6, '', '::1', NULL, '2025-06-10 19:52:39'),
(19, 'cadastro_cliente', 'Cliente #3 cadastrado', 6, '', '::1', NULL, '2025-06-10 19:55:10'),
(20, 'cadastro_cliente', 'Cliente #4 cadastrado', 6, '', '::1', NULL, '2025-06-10 21:31:35'),
(21, 'cliente_excluido', 'Cliente #4 excluído com sucesso', 6, '', '::1', NULL, '2025-06-10 21:33:17'),
(22, 'cliente_excluido', 'Cliente #2 excluído com sucesso', 6, '', '::1', NULL, '2025-06-10 21:33:25'),
(23, 'cliente_excluido', 'Cliente #3 excluído com sucesso', 6, '', '::1', NULL, '2025-06-10 21:33:33'),
(24, 'cadastro_cliente', 'Cliente #5 cadastrado', 6, '', '::1', NULL, '2025-06-10 21:33:55'),
(25, 'cliente_excluido', 'Cliente #5 excluído com sucesso', 6, '', '::1', NULL, '2025-06-10 21:36:25'),
(26, 'cadastro_cliente', 'Cliente #6 cadastrado', 6, '', '::1', NULL, '2025-06-10 21:36:45'),
(27, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-10 22:58:20'),
(28, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-10 22:58:24'),
(29, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-10 22:58:48'),
(30, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-10 22:59:47'),
(31, 'orcamento_excluido', 'Orçamento #9 excluído com sucesso', 6, '', '::1', NULL, '2025-06-10 23:01:55'),
(32, 'orcamento_excluido', 'Orçamento #11 excluído com sucesso', 6, '', '::1', NULL, '2025-06-10 23:02:37'),
(33, 'contato_respondido', 'Contato #1 respondido', 6, '', '::1', NULL, '2025-06-10 23:08:39'),
(34, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 19:18:35'),
(35, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 19:21:42'),
(36, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 19:40:58'),
(37, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 19:43:25'),
(38, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 20:58:30'),
(39, 'orcamento_aprovado', 'Orçamento #10 aprovado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:47:32'),
(40, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:47:47'),
(41, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:48:10'),
(42, 'orcamento_recusado', 'Orçamento #12 recusado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:48:38'),
(43, 'orcamento_aprovado', 'Orçamento #13 aprovado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:51:43'),
(44, 'orcamento_recusado', 'Orçamento #8 recusado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:53:23'),
(45, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:54:19'),
(46, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 21:58:41'),
(47, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 22:09:34'),
(48, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 22:10:05'),
(49, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 22:20:05'),
(50, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 22:32:56'),
(51, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 22:59:48'),
(52, 'login_falha', 'Tentativa de login falhou para o usuário: glaucoschneider', NULL, '', '::1', NULL, '2025-06-11 23:00:01'),
(53, 'login_falha', 'Tentativa de login falhou para o usuário: glaucoschneider', NULL, '', '::1', NULL, '2025-06-11 23:00:11'),
(54, 'login_falha', 'Tentativa de login falhou para o usuário: caetanosouto', NULL, '', '::1', NULL, '2025-06-11 23:00:22'),
(55, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 23:00:25'),
(56, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 23:01:55'),
(57, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 23:05:57'),
(58, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 23:08:48'),
(59, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-11 23:09:04'),
(60, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-12 20:25:36'),
(61, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-12 20:27:02'),
(62, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-12 23:01:27'),
(63, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-12 23:01:36'),
(64, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-12 23:03:57'),
(65, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-12 23:37:43'),
(66, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-14 17:25:04'),
(67, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-14 17:25:07'),
(68, 'login', 'Login realizado com sucesso', 10, '', '::1', NULL, '2025-06-14 17:25:11'),
(69, 'logout', 'Logout realizado com sucesso', 10, '', '::1', NULL, '2025-06-14 17:25:16'),
(70, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-14 17:25:23'),
(71, 'orcamento_aprovado', 'Orçamento #16 aprovado com sucesso', 6, '', '::1', NULL, '2025-06-14 17:29:20'),
(72, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-14 17:31:42'),
(73, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 19:29:51'),
(74, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 19:30:00'),
(75, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 20:16:40'),
(76, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 20:16:43'),
(77, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 20:16:47'),
(78, 'orcamento_aprovado', 'Orçamento #15 aprovado com sucesso', 6, '', '::1', NULL, '2025-06-16 20:17:21'),
(79, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 20:18:45'),
(80, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 20:19:05'),
(81, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-16 20:19:38'),
(82, 'login', 'Login realizado com sucesso', 6, '', '::1', NULL, '2025-06-21 20:23:21'),
(83, 'logout', 'Logout realizado com sucesso', 6, '', '::1', NULL, '2025-06-21 20:23:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos`
--

CREATE TABLE `orcamentos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `status` enum('pendente','respondido','aprovado','recusado','expirado') DEFAULT 'pendente',
  `data_solicitacao` date NOT NULL DEFAULT curdate(),
  `data_resposta` date DEFAULT NULL,
  `data_expiracao` date DEFAULT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp(),
  `servicos` text DEFAULT NULL,
  `data_aprovacao` date DEFAULT NULL,
  `data_recusa` date DEFAULT NULL,
  `motivo_recusa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orcamentos`
--

INSERT INTO `orcamentos` (`id`, `cliente_id`, `nome`, `email`, `telefone`, `mensagem`, `status`, `data_solicitacao`, `data_resposta`, `data_expiracao`, `data_criacao`, `servicos`, `data_aprovacao`, `data_recusa`, `motivo_recusa`) VALUES
(8, NULL, 'Paulo', 'teste@teste.com', '55999999999', 'ola', 'recusado', '2025-05-01', '2025-05-02', '2025-06-02', '2025-06-09 21:25:05', NULL, NULL, '2025-06-11', ''),
(10, NULL, 'Paulo Ricardo Costa Beber', 'paulo.37026@aluno.iffar.edu.br', '55996969580', '', '', '2025-06-09', '2025-06-09', '2025-07-09', '2025-06-09 22:25:50', NULL, '2025-06-11', NULL, NULL),
(12, NULL, 'Paulo', 'teste@teste.com', '55999999999', '', '', '2025-06-11', '2025-06-11', '2025-07-11', '2025-06-11 21:47:56', NULL, NULL, '2025-06-11', ''),
(13, NULL, 'Paulo', 'teste@teste.com', '55999999999', '', 'aprovado', '2025-06-11', '2025-06-11', '2025-07-11', '2025-06-11 21:48:05', NULL, '2025-06-11', NULL, NULL),
(14, NULL, 'Teste orçamento', 'teste@teste.com', '55999999999', '', 'respondido', '2025-05-11', '2025-05-11', '2025-06-12', '2025-06-11 22:09:52', NULL, NULL, NULL, NULL),
(15, NULL, 'Teste orçamento', 'teste@teste.com', '55999999999', '', 'aprovado', '2025-06-11', '2025-06-11', '2025-07-11', '2025-06-11 22:10:02', NULL, '2025-06-16', NULL, NULL),
(16, NULL, 'Teste orçamento', 'teste@teste.com', '55999999999', '', 'aprovado', '2025-06-11', '2025-06-14', '2025-07-14', '2025-06-11 22:39:17', NULL, '2025-06-14', NULL, NULL),
(17, NULL, 'Teste orçamento', 'teste@teste.com', '55999999999', '', 'respondido', '2025-06-11', '2025-06-11', '2025-07-11', '2025-06-11 23:08:57', NULL, NULL, NULL, NULL),
(18, NULL, 'Teste orçamento', 'teste@teste.com', '55999999999', 'ola', 'respondido', '2025-06-16', '2025-06-16', '2025-07-16', '2025-06-16 20:19:01', NULL, NULL, NULL, NULL);

--
-- Acionadores `orcamentos`
--
DELIMITER $$
CREATE TRIGGER `set_data_expiracao` BEFORE UPDATE ON `orcamentos` FOR EACH ROW BEGIN
    IF NEW.data_resposta IS NOT NULL AND NEW.data_expiracao IS NULL THEN
        SET NEW.data_expiracao = DATE_ADD(NEW.data_resposta, INTERVAL 30 DAY);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos_arquivos`
--

CREATE TABLE `orcamentos_arquivos` (
  `id` int(11) NOT NULL,
  `orcamento_id` int(11) NOT NULL,
  `nome_original` varchar(255) NOT NULL,
  `nome_sistema` varchar(255) NOT NULL,
  `caminho` varchar(255) NOT NULL,
  `data_upload` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orcamentos_arquivos`
--

INSERT INTO `orcamentos_arquivos` (`id`, `orcamento_id`, `nome_original`, `nome_sistema`, `caminho`, `data_upload`) VALUES
(2, 8, '02.pdf', 'arquivo_68477b6194cc1.pdf', 'uploads/orcamentos/arquivo_68477b6194cc1.pdf', '2025-06-09 21:25:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos_respostas`
--

CREATE TABLE `orcamentos_respostas` (
  `id` int(11) NOT NULL,
  `orcamento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `resposta` text NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_resposta` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orcamentos_respostas`
--

INSERT INTO `orcamentos_respostas` (`id`, `orcamento_id`, `usuario_id`, `resposta`, `valor`, `data_resposta`) VALUES
(11, 10, 6, 'mediante avaliação', 2000.00, '2025-06-09'),
(12, 12, 6, 'gfhghf', 2000.00, '2025-06-11'),
(13, 13, 6, 'ola', 2000.00, '2025-06-11'),
(14, 8, 6, 'vjvjvj', 2000.00, '2025-06-11'),
(15, 14, 6, 'Aguardando aprovação!', 2000.00, '2025-06-11'),
(16, 15, 6, 'mediante avaliação', 5000.00, '2025-06-11'),
(17, 17, 6, 'ghfgh', 2000.00, '2025-06-11'),
(18, 16, 6, 'avaliado', 2000.00, '2025-06-14'),
(19, 18, 6, 'gdfg', 2000.00, '2025-06-16');

--
-- Acionadores `orcamentos_respostas`
--
DELIMITER $$
CREATE TRIGGER `after_orcamento_resposta` AFTER INSERT ON `orcamentos_respostas` FOR EACH ROW BEGIN
    UPDATE orcamentos SET 
        status = 'respondido',
        data_resposta = NEW.data_resposta
    WHERE id = NEW.orcamento_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orcamentos_servicos`
--

CREATE TABLE `orcamentos_servicos` (
  `id` int(11) NOT NULL,
  `orcamento_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orcamentos_servicos`
--

INSERT INTO `orcamentos_servicos` (`id`, `orcamento_id`, `servico_id`) VALUES
(9, 8, 3),
(11, 10, 3),
(13, 12, 5),
(14, 13, 4),
(15, 14, 3),
(16, 15, 3),
(17, 16, 4),
(18, 17, 2),
(19, 18, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `nome`, `descricao`, `slug`, `ativo`) VALUES
(1, 'Sistemas de Monitoramento', 'Venda, instalação e manutenção de câmeras de monitoramento para residências e empresas.', 'sistemas-monitoramento', 1),
(2, 'Instalações Elétricas', 'Instalação e manutenção de instalações elétricas residenciais com qualidade e segurança.', 'instalacoes-eletricas', 1),
(3, 'Automação', 'Desenvolvimento de sistemas de automação residenciais e industriais para maior eficiência.', 'automacao', 1),
(4, 'Alarmes', 'Instalação e manutenção de sistemas de alarme para maior segurança do seu patrimônio.', 'alarmes', 1),
(5, 'Portões Eletrônicos', 'Instalação e manutenção de sistemas elétricos para portões eletrônicos.', 'portoes-eletronicos', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel_acesso` enum('admin','gerente') NOT NULL DEFAULT 'admin',
  `data_cadastro` date NOT NULL DEFAULT curdate(),
  `ultimo_acesso` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `usuario`, `email`, `senha`, `nivel_acesso`, `data_cadastro`, `ultimo_acesso`) VALUES
(5, 'Glauco Schneider', 'glaucoschneider', 'glauco.2022005150@aluno.iffar.edu.br', '$2y$10$qJQFUdtLxGsVPRIwpTHBYeRhGBnM7jLIJGXoFZUfA5W5/TwPRVx4y', 'admin', '2025-06-04', NULL),
(6, 'Paulo Ricardo', 'pauloricardo', 'paulo.37026@aluno.iffar.edu.br', '$2y$10$zB31NavoWDN0n.0dpNRPu.cGT7JubLJiwHSzO0Eq.HKdwwEebw.D2', 'admin', '2025-06-04', '2025-06-21'),
(7, 'Caetano Souto', 'caetanosouto', 'caetano-souto1@hotmail.com', '$2y$10$qJQFUdtLxGsVPRIwpTHBYeRhGBnM7jLIJGXoFZUfA5W5/TwPRVx4y', 'admin', '2025-06-04', NULL),
(10, 'Administrador', 'admin', 'admin@ideaservices.com.br', '$2y$10$zB31NavoWDN0n.0dpNRPu.cGT7JubLJiwHSzO0Eq.HKdwwEebw.D2', 'admin', '2025-06-05', '2025-06-14');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_telefone` (`telefone`);

--
-- Índices de tabela `contatos`
--
ALTER TABLE `contatos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_setor` (`setor`),
  ADD KEY `idx_cliente_id` (`cliente_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_cliente_id` (`cliente_id`),
  ADD KEY `idx_data_solicitacao` (`data_solicitacao`);

--
-- Índices de tabela `orcamentos_arquivos`
--
ALTER TABLE `orcamentos_arquivos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orcamento_id` (`orcamento_id`);

--
-- Índices de tabela `orcamentos_respostas`
--
ALTER TABLE `orcamentos_respostas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orcamento_id` (`orcamento_id`),
  ADD KEY `idx_usuario_id` (`usuario_id`);

--
-- Índices de tabela `orcamentos_servicos`
--
ALTER TABLE `orcamentos_servicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_orcamento_servico` (`orcamento_id`,`servico_id`),
  ADD KEY `servico_id` (`servico_id`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_ativo` (`ativo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuario` (`usuario`),
  ADD KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `contatos`
--
ALTER TABLE `contatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de tabela `orcamentos`
--
ALTER TABLE `orcamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `orcamentos_arquivos`
--
ALTER TABLE `orcamentos_arquivos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `orcamentos_respostas`
--
ALTER TABLE `orcamentos_respostas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `orcamentos_servicos`
--
ALTER TABLE `orcamentos_servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `contatos`
--
ALTER TABLE `contatos`
  ADD CONSTRAINT `contatos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `orcamentos`
--
ALTER TABLE `orcamentos`
  ADD CONSTRAINT `orcamentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `orcamentos_arquivos`
--
ALTER TABLE `orcamentos_arquivos`
  ADD CONSTRAINT `orcamentos_arquivos_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamentos_respostas`
--
ALTER TABLE `orcamentos_respostas`
  ADD CONSTRAINT `orcamentos_respostas_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamentos_respostas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orcamentos_servicos`
--
ALTER TABLE `orcamentos_servicos`
  ADD CONSTRAINT `orcamentos_servicos_ibfk_1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamentos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orcamentos_servicos_ibfk_2` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `evento_expirar_orcamentos` ON SCHEDULE EVERY 1 DAY STARTS '2025-06-04 19:30:37' ON COMPLETION NOT PRESERVE ENABLE DO CALL expirar_orcamentos_antigos()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
