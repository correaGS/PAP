-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 22-Fev-2022 às 21:03
-- Versão do servidor: 10.4.21-MariaDB
-- versão do PHP: 8.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `basededados`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `categoria`
--

INSERT INTO `categoria` (`id`, `descricao`, `parent_id`) VALUES
(2, 'programação', NULL),
(3, 'SQL', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_reply` int(11) DEFAULT NULL COMMENT 'Id de outro comentário, caso seja uma resposta, se for nulo o comentário é para o post',
  `text` varchar(400) NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `comment`
--

INSERT INTO `comment` (`id`, `user`, `id_post`, `id_reply`, `text`, `data_hora`) VALUES
(1, 'teste', 16, NULL, 'teste', '2022-02-08 11:11:48'),
(2, 'teste', 16, 1, 'teste reply', '2022-02-08 11:12:42');

--
-- Acionadores `comment`
--
DELIMITER $$
CREATE TRIGGER `after_comment_insert` AFTER INSERT ON `comment` FOR EACH ROW BEGIN
        INSERT INTO count_like_comment(id_comment, likes, data_hora)
        VALUES(new.id, 0, CURRENT_TIMESTAMP);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `comment_like`
--

CREATE TABLE `comment_like` (
  `id_comment` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `vote` smallint(6) NOT NULL COMMENT '1 = like / -1 = dislike',
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Acionadores `comment_like`
--
DELIMITER $$
CREATE TRIGGER `commentLikeCountDelete` AFTER DELETE ON `comment_like` FOR EACH ROW update count_like_comment c
SET c.likes = (SELECT sum(vote) from comment_like p WHERE p.id_comment = old.id_comment)
where c.id_comment = old.id_comment
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `commentLikeCountInsert` AFTER INSERT ON `comment_like` FOR EACH ROW update count_like_comment c
SET c.likes = (SELECT sum(vote) from comment_like p WHERE p.id_comment = new.id_comment)
where c.id_comment = new.id_comment
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `commentLikeCountUpdate` AFTER UPDATE ON `comment_like` FOR EACH ROW update count_like_comment c
SET c.likes = (SELECT sum(vote) from comment_like p WHERE p.id_comment = new.id_comment)
where c.id_comment = new.id_comment
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `count_like_comment`
--

CREATE TABLE `count_like_comment` (
  `id_comment` int(11) NOT NULL,
  `likes` int(11) NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `count_like_comment`
--

INSERT INTO `count_like_comment` (`id_comment`, `likes`, `data_hora`) VALUES
(1, 0, '2022-02-08 11:20:24'),
(2, 0, '2022-02-08 11:20:20');

-- --------------------------------------------------------

--
-- Estrutura da tabela `count_like_post`
--

CREATE TABLE `count_like_post` (
  `id_post` int(11) NOT NULL,
  `likes` int(11) NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `count_like_post`
--

INSERT INTO `count_like_post` (`id_post`, `likes`, `data_hora`) VALUES
(16, -1, '2022-02-09 08:44:45');

-- --------------------------------------------------------

--
-- Estrutura da tabela `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `post` varchar(3000) NOT NULL,
  `visibilidade` int(11) DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `post`
--

INSERT INTO `post` (`id`, `username`, `titulo`, `post`, `visibilidade`, `data_hora`) VALUES
(2, 'teste4', NULL, 'easrdjfkhvb jnklmç', 1, '2021-12-04 15:37:24'),
(4, 'teste5', NULL, 'egsbhsrndfs\\r\\nxfncgnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn\\r\\ncgncgncnc\\r\\ncgngggggggggggggggggggggggggggggggggggggggggggggggg', 1, '2021-12-04 16:39:08'),
(5, 'teste5', NULL, 'wehersehw\\r\\nsehdrhzdr\\r\\nsrhdb', 1, '2021-12-04 16:44:24'),
(6, 'teste5', NULL, 'testando criar novas publicações', 1, '2021-12-04 16:46:29'),
(10, 'teste5', NULL, 'fghj\\r\\ncvbnm', 1, '2021-12-04 17:00:19'),
(11, 'teste5', NULL, 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. lectus sit amet est placerat. consectetur adipiscing elit ut aliquam purus. aliquam purus sit amet luctus venenatis. quam id leo in vitae turpis massa sed elementum tempus. morbi quis commodo odio aenean sed adipiscing. at varius vel pharetra vel. bibendum at varius vel pharetra. tristique senectus et netus et malesuada. sagittis eu volutpat odio facilisis. sed vulputate mi sit amet mauris commodo. pellentesque habitant morbi tristique senectus et netus. cursus mattis molestie a iaculis at.\\r\\n\\r\\nmassa sapien faucibus et molestie ac feugiat sed lectus. non blandit massa enim nec dui nunc mattis enim ut. ipsum a arcu cursus vitae congue mauris. morbi tristique senectus et netus. quisque sagittis purus sit amet volutpat consequat mauris nunc. elit pellentesque habitant morbi tristique senectus et netus et. tellus in hac habitasse platea dictumst vestibulum rhoncus est. turpis nunc eget lorem dolor sed viverra. morbi tincidunt augue interdum velit euismod. leo vel fringilla est ullamcorper eget nulla facilisi etiam dignissim. condimentum id venenatis a condimentum vitae sapien. cursus in hac habitasse platea dictumst quisque sagittis. enim facilisis gravida neque convallis a cras semper auctor neque. sed nisi lacus sed viverra.', 1, '2021-12-04 17:11:08'),
(13, 'teste15', NULL, 'ruhdrhdztgnfgdndndfndf', 1, '2021-12-11 19:42:36'),
(16, 'teste', NULL, 'teste', 1, '2022-02-02 16:16:39');

--
-- Acionadores `post`
--
DELIMITER $$
CREATE TRIGGER `after_post_insert` AFTER INSERT ON `post` FOR EACH ROW BEGIN
        INSERT INTO count_like_post(id_post, likes, data_hora)
        VALUES(new.id, 0, CURRENT_TIMESTAMP);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `post_categoria`
--

CREATE TABLE `post_categoria` (
  `id_post` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `post_like`
--

CREATE TABLE `post_like` (
  `id_post` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `vote` smallint(6) NOT NULL COMMENT '1 = like / -1 = dislike',
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `post_like`
--

INSERT INTO `post_like` (`id_post`, `user`, `vote`, `data_hora`) VALUES
(16, 'teste', -1, '2022-02-09 08:44:45');

--
-- Acionadores `post_like`
--
DELIMITER $$
CREATE TRIGGER `postLikeCountDelete` AFTER DELETE ON `post_like` FOR EACH ROW update count_like_post c
SET c.likes = (SELECT sum(vote) from post_like p WHERE p.id_post = old.id_post)
where c.id_post = old.id_post
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `postLikeCountInsert` AFTER INSERT ON `post_like` FOR EACH ROW update count_like_post c
SET c.likes = (SELECT sum(vote) from post_like p WHERE p.id_post = new.id_post)
where c.id_post = new.id_post
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `postLikeCountUpdate` AFTER UPDATE ON `post_like` FOR EACH ROW update count_like_post c
SET c.likes = (SELECT sum(vote) from post_like p WHERE p.id_post = new.id_post)
where c.id_post = new.id_post
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `profile_genero`
--

CREATE TABLE `profile_genero` (
  `id_genero` int(11) NOT NULL,
  `descricao` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `profile_genero`
--

INSERT INTO `profile_genero` (`id_genero`, `descricao`) VALUES
(1, 'masculino'),
(2, 'feminino');

-- --------------------------------------------------------

--
-- Estrutura da tabela `profile_nacionalidade`
--

CREATE TABLE `profile_nacionalidade` (
  `id_nacionalidade` int(11) NOT NULL,
  `descricao` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `profile_nacionalidade`
--

INSERT INTO `profile_nacionalidade` (`id_nacionalidade`, `descricao`) VALUES
(1, 'português'),
(2, 'brasileiro');

-- --------------------------------------------------------

--
-- Estrutura da tabela `profile_visibility`
--

CREATE TABLE `profile_visibility` (
  `id_visibilidade` int(11) NOT NULL,
  `descricao` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `profile_visibility`
--

INSERT INTO `profile_visibility` (`id_visibilidade`, `descricao`) VALUES
(1, 'público'),
(2, 'privado'),
(5, 'Seguidores'),
(6, 'Moderadores'),
(7, 'Administradores');

-- --------------------------------------------------------

--
-- Estrutura da tabela `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `post_reported` int(11) DEFAULT NULL,
  `comment_reported` int(11) DEFAULT NULL,
  `user_reported` varchar(100) DEFAULT NULL,
  `profile_reported` varchar(100) DEFAULT NULL,
  `motivo` int(11) NOT NULL,
  `outro` varchar(500) DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `report_motivo`
--

CREATE TABLE `report_motivo` (
  `id` int(11) NOT NULL,
  `descricao` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `report_motivo`
--

INSERT INTO `report_motivo` (`id`, `descricao`) VALUES
(1, 'spam'),
(2, 'discurso de ódio'),
(3, 'assédio'),
(4, 'bullying'),
(5, 'conteúdo violento e explícito'),
(6, 'informações enganosas'),
(7, 'outro');

-- --------------------------------------------------------

--
-- Estrutura da tabela `seguir_categoria`
--

CREATE TABLE `seguir_categoria` (
  `user` varchar(100) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `seguir_user`
--

CREATE TABLE `seguir_user` (
  `user` varchar(100) NOT NULL,
  `user_seguido` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

CREATE TABLE `user` (
  `username` varchar(100) CHARACTER SET utf8mb4 NOT NULL COMMENT 'único',
  `email` varchar(250) CHARACTER SET utf8mb4 NOT NULL COMMENT 'único',
  `password` varchar(100) CHARACTER SET utf8mb4 NOT NULL COMMENT 'Encriptado',
  `nivel` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `token` varchar(250) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'Codificado',
  `marketing` int(11) NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'timestamp',
  `atividade` int(11) NOT NULL,
  `ultima_aividade` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`username`, `email`, `password`, `nivel`, `status`, `token`, `marketing`, `data_hora`, `atividade`, `ultima_aividade`) VALUES
('teste', 'testeEmail', 'teste', 1, 2, '', 2, '2021-11-29 19:13:00', 2, '2022-02-16 10:33:10'),
('teste10', 'teste10@teste.com', '$2y$10$ByEp4L2D2AxNwawm1HumZuisOF42gMtLmEvEBYQ6sup1097jvzamW', 1, 2, '', 2, '2021-11-29 19:56:41', 2, NULL),
('teste11', 'teste11@teste.com', '$2y$10$obHJNZSqI9w8A/Vbxnf4Ru8OPWUKMNuK95jhkpWs6m3ZKCIwmWMmS', 1, 2, '', 2, '2021-12-04 18:24:40', 2, NULL),
('teste12', 'teste12@teste.com', '$2y$10$FCJCI5RGBC2/5oyXzd15nuAvm80fYizckFc0ZLVvS83qAp0kDwkmK', 1, 2, '', 2, '2021-12-06 19:01:22', 2, NULL),
('teste13', 'teste13@teste.com', '$2y$10$ltP6PpPEhIK85gyp4sXVdewV/hbpitfAbflXOAdWmIyM9rxJ1t8Oa', 1, 2, '', 2, '2021-12-06 19:09:01', 2, NULL),
('teste14', 'teste14@teste.com', '$2y$10$i9tEfSEAo6af2frVAExOsuQWlmXfj8R42sbUZCb0HSaR/Zb3drs9u', 1, 2, '', 1, '2021-12-06 19:36:50', 2, NULL),
('teste15', 'teste15@teste.com', '$2y$10$5SdV2azzCpgAOPjx2cUZAePesPmwAzkaOMoP3oAS76pWkSk9CPt6C', 1, 2, '', 2, '2021-12-11 17:47:16', 2, NULL),
('teste2', 'teste2', 'teste2', 1, 2, '', 1, '2021-11-27 21:39:00', 2, NULL),
('teste3', 'teste3@teste.com', 'teste', 1, 2, '', 2, '2021-11-27 21:39:01', 2, NULL),
('teste4', 'teste4@teste.com', '$2y$10$b3rIV8XqCUg65otX6xbW4eayZH6AwOgr3pAKpq7wT07fVkOelfcKK', 3, 2, '', 1, '2021-11-29 19:40:52', 2, NULL),
('teste5', 'teste5@teste.com', '$2y$10$mjB2jpwH1JGmLkb486QTs.VEwLsYwrxwfJ3dyB78zeVIypPpVSGnK', 1, 2, '', 2, '2021-11-28 14:37:12', 2, NULL),
('teste6', 'teste6@teste.com', '$2y$10$8QvfdfjSM2npqZjKYw8dfec62eFcamJi1zz/yh9/AEU4DEPjzrU82', 1, 2, '', 1, '2021-11-28 17:22:08', 2, NULL),
('teste7', 'teste7@teste.com', '$2y$10$RTQ1UgvwDcgT4tQ5h3gaX.cUaTfz9lCX6PF9ofBxOpdkbk9ppwkg6', 1, 2, '', 1, '2021-11-28 17:39:48', 2, NULL),
('teste8', 'teste8@teste.com', '$2y$10$Cuoxyb0JJ.wLEkznAPttiebsfIBoo6bU3mb/J/xnIaFj4EzvyDaCG', 1, 2, '', 1, '2021-11-28 19:20:11', 2, NULL),
('teste9', 'teste9@teste.com', '$2y$10$t4EJj17gzw97JOQCrIn.TeE7eOEnvZd..9iS9baFikD3Fb9726nE6', 1, 2, '', 2, '2021-11-29 19:50:09', 2, NULL);

--
-- Acionadores `user`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `user` FOR EACH ROW BEGIN
        INSERT INTO user_profile(username, visibilidade)
        VALUES(new.username, 2);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_atividade`
--

CREATE TABLE `user_atividade` (
  `id` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `user_atividade`
--

INSERT INTO `user_atividade` (`id`, `descricao`) VALUES
(1, 'online'),
(2, 'offline');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_marketing`
--

CREATE TABLE `user_marketing` (
  `id_marketing` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `user_marketing`
--

INSERT INTO `user_marketing` (`id_marketing`, `descricao`) VALUES
(1, 'não aceito'),
(2, 'aceito');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_nivel`
--

CREATE TABLE `user_nivel` (
  `id_nivel` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `user_nivel`
--

INSERT INTO `user_nivel` (`id_nivel`, `descricao`) VALUES
(1, 'padrão'),
(2, 'moderador'),
(3, 'administrador'),
(6, 'builder');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_profile`
--

CREATE TABLE `user_profile` (
  `username` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `nome` varchar(150) CHARACTER SET utf8mb4 NOT NULL,
  `apelido` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `nacionalidade` int(11) DEFAULT NULL,
  `genero` int(11) DEFAULT NULL,
  `telemovel` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'codificado',
  `foto` varchar(500) CHARACTER SET utf8mb4 DEFAULT NULL,
  `bio` varchar(500) DEFAULT NULL,
  `visibilidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `user_profile`
--

INSERT INTO `user_profile` (`username`, `nome`, `apelido`, `nacionalidade`, `genero`, `telemovel`, `foto`, `bio`, `visibilidade`) VALUES
('teste', 'teste9', '', NULL, NULL, '', NULL, NULL, 1),
('teste10', '', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste11', 'teste11', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste12', 'teste12', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste13', 'teste13', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste14', 'teste14', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste15', 'teste15', '', NULL, NULL, '', './imagens/teste15/20200921_160940.jpg', NULL, 2),
('teste2', 'teste9', 'teste', NULL, NULL, '000000000', NULL, NULL, 1),
('teste3', 'teste9', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste4', 'teste4', '', NULL, NULL, '351964946517', NULL, NULL, 1),
('teste5', 'teste5', 'teste', NULL, NULL, '000000000000', NULL, NULL, 1),
('teste6', 'teste9', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste7', 'teste9', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste8', 'teste9', NULL, NULL, NULL, NULL, NULL, NULL, 2),
('teste9', 'teste9', NULL, NULL, NULL, NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `user_status`
--

CREATE TABLE `user_status` (
  `id_status` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `user_status`
--

INSERT INTO `user_status` (`id_status`, `descricao`) VALUES
(1, 'não ativo'),
(2, 'ativo'),
(3, 'banido');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Índices para tabela `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_user` (`user`),
  ADD KEY `comment_post` (`id_post`),
  ADD KEY `comment_reply` (`id_reply`);

--
-- Índices para tabela `comment_like`
--
ALTER TABLE `comment_like`
  ADD PRIMARY KEY (`id_comment`,`user`),
  ADD KEY `like_comment_user` (`user`);

--
-- Índices para tabela `count_like_comment`
--
ALTER TABLE `count_like_comment`
  ADD KEY `count_like_comment` (`id_comment`);

--
-- Índices para tabela `count_like_post`
--
ALTER TABLE `count_like_post`
  ADD PRIMARY KEY (`id_post`);

--
-- Índices para tabela `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user-post` (`username`),
  ADD KEY `post_visibilidade` (`visibilidade`);

--
-- Índices para tabela `post_categoria`
--
ALTER TABLE `post_categoria`
  ADD PRIMARY KEY (`id_post`,`id_categoria`),
  ADD KEY `id_categoria_post` (`id_categoria`);

--
-- Índices para tabela `post_like`
--
ALTER TABLE `post_like`
  ADD PRIMARY KEY (`id_post`,`user`),
  ADD KEY `like_user` (`user`);

--
-- Índices para tabela `profile_genero`
--
ALTER TABLE `profile_genero`
  ADD PRIMARY KEY (`id_genero`);

--
-- Índices para tabela `profile_nacionalidade`
--
ALTER TABLE `profile_nacionalidade`
  ADD PRIMARY KEY (`id_nacionalidade`);

--
-- Índices para tabela `profile_visibility`
--
ALTER TABLE `profile_visibility`
  ADD PRIMARY KEY (`id_visibilidade`);

--
-- Índices para tabela `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_post` (`post_reported`),
  ADD KEY `report_comment` (`comment_reported`),
  ADD KEY `report_user` (`user_reported`),
  ADD KEY `report_profile` (`profile_reported`),
  ADD KEY `report_motivo` (`motivo`);

--
-- Índices para tabela `report_motivo`
--
ALTER TABLE `report_motivo`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `seguir_categoria`
--
ALTER TABLE `seguir_categoria`
  ADD PRIMARY KEY (`user`,`id_categoria`),
  ADD KEY `categoria_seguida` (`id_categoria`);

--
-- Índices para tabela `seguir_user`
--
ALTER TABLE `seguir_user`
  ADD PRIMARY KEY (`user`,`user_seguido`),
  ADD KEY `user_seguido` (`user_seguido`);

--
-- Índices para tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_nivel` (`nivel`),
  ADD KEY `fk_status` (`status`),
  ADD KEY `fk_marketing` (`marketing`),
  ADD KEY `fk_atividade` (`atividade`);

--
-- Índices para tabela `user_atividade`
--
ALTER TABLE `user_atividade`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `user_marketing`
--
ALTER TABLE `user_marketing`
  ADD PRIMARY KEY (`id_marketing`);

--
-- Índices para tabela `user_nivel`
--
ALTER TABLE `user_nivel`
  ADD PRIMARY KEY (`id_nivel`);

--
-- Índices para tabela `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`username`),
  ADD KEY `fk_visibilidade` (`visibilidade`),
  ADD KEY `fk_genero` (`genero`),
  ADD KEY `fk_nacionalidade` (`nacionalidade`);

--
-- Índices para tabela `user_status`
--
ALTER TABLE `user_status`
  ADD PRIMARY KEY (`id_status`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `profile_genero`
--
ALTER TABLE `profile_genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `profile_nacionalidade`
--
ALTER TABLE `profile_nacionalidade`
  MODIFY `id_nacionalidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `profile_visibility`
--
ALTER TABLE `profile_visibility`
  MODIFY `id_visibilidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `report_motivo`
--
ALTER TABLE `report_motivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `user_atividade`
--
ALTER TABLE `user_atividade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `user_marketing`
--
ALTER TABLE `user_marketing`
  MODIFY `id_marketing` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `user_nivel`
--
ALTER TABLE `user_nivel`
  MODIFY `id_nivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `user_status`
--
ALTER TABLE `user_status`
  MODIFY `id_status` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `categoria`
--
ALTER TABLE `categoria`
  ADD CONSTRAINT `parent_id` FOREIGN KEY (`parent_id`) REFERENCES `categoria` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_post` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_reply` FOREIGN KEY (`id_reply`) REFERENCES `comment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_user` FOREIGN KEY (`user`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `comment_like`
--
ALTER TABLE `comment_like`
  ADD CONSTRAINT `like_comment` FOREIGN KEY (`id_comment`) REFERENCES `comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `like_comment_user` FOREIGN KEY (`user`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `count_like_comment`
--
ALTER TABLE `count_like_comment`
  ADD CONSTRAINT `count_like_comment` FOREIGN KEY (`id_comment`) REFERENCES `comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `count_like_post`
--
ALTER TABLE `count_like_post`
  ADD CONSTRAINT `count_like_post` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_user-post` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_visibilidade` FOREIGN KEY (`visibilidade`) REFERENCES `profile_visibility` (`id_visibilidade`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `post_categoria`
--
ALTER TABLE `post_categoria`
  ADD CONSTRAINT `id_categoria_post` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_post_categoria` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `post_like`
--
ALTER TABLE `post_like`
  ADD CONSTRAINT `like_post` FOREIGN KEY (`id_post`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `like_post_user` FOREIGN KEY (`user`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_comment` FOREIGN KEY (`comment_reported`) REFERENCES `comment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_motivo` FOREIGN KEY (`motivo`) REFERENCES `report_motivo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_post` FOREIGN KEY (`post_reported`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_profile` FOREIGN KEY (`profile_reported`) REFERENCES `user` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_user` FOREIGN KEY (`user_reported`) REFERENCES `user` (`username`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `seguir_categoria`
--
ALTER TABLE `seguir_categoria`
  ADD CONSTRAINT `categoria_seguida` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_segue` FOREIGN KEY (`user`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `seguir_user`
--
ALTER TABLE `seguir_user`
  ADD CONSTRAINT `user_atual` FOREIGN KEY (`user`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_seguido` FOREIGN KEY (`user_seguido`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_atividade` FOREIGN KEY (`atividade`) REFERENCES `user_atividade` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_marketing` FOREIGN KEY (`marketing`) REFERENCES `user_marketing` (`id_marketing`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nivel` FOREIGN KEY (`nivel`) REFERENCES `user_nivel` (`id_nivel`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_status` FOREIGN KEY (`status`) REFERENCES `user_status` (`id_status`) ON UPDATE CASCADE;

--
-- Limitadores para a tabela `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `fk_genero` FOREIGN KEY (`genero`) REFERENCES `profile_genero` (`id_genero`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nacionalidade` FOREIGN KEY (`nacionalidade`) REFERENCES `profile_nacionalidade` (`id_nacionalidade`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_visibilidade` FOREIGN KEY (`visibilidade`) REFERENCES `profile_visibility` (`id_visibilidade`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
