-- Plataforma Editorial Plash
-- Estrutura do Banco de Dados
-- Versão: 1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','athlete','collaborator','partner') NOT NULL DEFAULT 'athlete',
  `sub_role` varchar(50) DEFAULT NULL,
  `status` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `badges` json DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `token` (`token`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `edicoes`
--

CREATE TABLE `edicoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `periodicidade` varchar(50) NOT NULL DEFAULT 'mensal',
  `status` enum('criacao','aguardando_envio','entregue','aprovado','lancado') NOT NULL DEFAULT 'criacao',
  `tipo` enum('digital','impresso') NOT NULL DEFAULT 'digital',
  `formato` varchar(50) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `status` (`status`),
  KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `capas`
--

CREATE TABLE `capas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) NOT NULL,
  `athlete_id` int(11) NOT NULL,
  `collaborator_id` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `status` enum('criacao','aguardando_envio','entregue','aprovado','lancado') NOT NULL DEFAULT 'criacao',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `edition_id` (`edition_id`),
  KEY `athlete_id` (`athlete_id`),
  KEY `collaborator_id` (`collaborator_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`edition_id`) REFERENCES `edicoes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`athlete_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`collaborator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `edition_id` int(11) DEFAULT NULL,
  `cover_id` int(11) DEFAULT NULL,
  `tipo` enum('video','photo','pdf','zip') NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `status` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `rejection_reason` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `edition_id` (`edition_id`),
  KEY `cover_id` (`cover_id`),
  KEY `status` (`status`),
  KEY `tipo` (`tipo`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`edition_id`) REFERENCES `edicoes` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`cover_id`) REFERENCES `capas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrevistas`
--

CREATE TABLE `entrevistas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `edition_id` int(11) DEFAULT NULL,
  `cover_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `questions` json NOT NULL,
  `answers` json DEFAULT NULL,
  `status` enum('aguardando','aprovado','reprovado') NOT NULL DEFAULT 'aguardando',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `edition_id` (`edition_id`),
  KEY `cover_id` (`cover_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`edition_id`) REFERENCES `edicoes` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`cover_id`) REFERENCES `capas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `contratos`
--

CREATE TABLE `contratos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `edition_id` int(11) DEFAULT NULL,
  `cover_id` int(11) DEFAULT NULL,
  `tipo` varchar(100) NOT NULL,
  `content` longtext NOT NULL,
  `status` enum('pendente','assinado') NOT NULL DEFAULT 'pendente',
  `signed_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `user_id` (`user_id`),
  KEY `edition_id` (`edition_id`),
  KEY `cover_id` (`cover_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`edition_id`) REFERENCES `edicoes` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`cover_id`) REFERENCES `capas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos_digitais`
--

CREATE TABLE `produtos_digitais` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `quality` varchar(50) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `observations` text DEFAULT NULL,
  `status` enum('criacao','aguardando_envio','entregue','aprovado','lancado') NOT NULL DEFAULT 'criacao',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `edition_id` (`edition_id`),
  KEY `partner_id` (`partner_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`edition_id`) REFERENCES `edicoes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`partner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos_impressos`
--

CREATE TABLE `produtos_impressos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `edition_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `size` varchar(50) NOT NULL,
  `spine_type` varchar(50) NOT NULL,
  `weight` decimal(8,2) NOT NULL,
  `pages` int(11) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `observations` text DEFAULT NULL,
  `status` enum('criacao','aguardando_envio','entregue','aprovado','lancado') NOT NULL DEFAULT 'criacao',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `edition_id` (`edition_id`),
  KEY `partner_id` (`partner_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`edition_id`) REFERENCES `edicoes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`partner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `indicacoes_participantes`
--

CREATE TABLE `indicacoes_participantes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `indicated_by` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `story` text NOT NULL,
  `intended_role` varchar(50) NOT NULL,
  `status` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `indicated_by` (`indicated_by`),
  KEY `status` (`status`),
  FOREIGN KEY (`indicated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `comissoes`
--

CREATE TABLE `comissoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collaborator_id` int(11) NOT NULL,
  `edition_id` int(11) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pendente','pago') NOT NULL DEFAULT 'pendente',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `collaborator_id` (`collaborator_id`),
  KEY `edition_id` (`edition_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`collaborator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`edition_id`) REFERENCES `edicoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','warning','error','success') NOT NULL DEFAULT 'info',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `read_at` (`read_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `logs_atividade`
--

CREATE TABLE `logs_atividade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `entity_type` (`entity_type`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Inserir dados de demonstração
--

-- Usuários de demonstração
INSERT INTO `users` (`name`, `email`, `password`, `role`, `sub_role`, `status`, `badges`, `bio`) VALUES
('Atleta Demo', 'atleta@plash.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'athlete', NULL, 'ativo', '["verificado"]', 'Skatista profissional há 10 anos'),
('Colaborador Demo', 'colaborador@plash.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'collaborator', NULL, 'ativo', '["verificado", "responsavel_proativo"]', 'Fotógrafo especializado em skate'),
('Editora Demo', 'editora@plash.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'partner', NULL, 'ativo', '["verificado", "compromisso_editorial"]', 'Editora parceira especializada em revistas de skate');

-- Edições de demonstração
INSERT INTO `edicoes` (`name`, `number`, `periodicidade`, `status`, `tipo`) VALUES
('Edição Verão 2024', 1, 'mensal', 'criacao', 'digital'),
('Edição Outono 2024', 2, 'mensal', 'aguardando_envio', 'digital');

COMMIT;