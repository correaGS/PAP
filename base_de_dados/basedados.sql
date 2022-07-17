-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17-Jul-2022 às 23:28
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
-- Banco de dados: `basedados`
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
(2, 'Programação', NULL),
(3, 'SQL', 2),
(4, 'Matemática', NULL),
(5, 'Física', NULL),
(6, 'Química', NULL),
(7, 'Inglês', NULL),
(8, 'Português', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `id_post` int(11) NOT NULL,
  `id_reply` int(11) DEFAULT NULL COMMENT 'Id de outro comentário, caso seja uma resposta, se for nulo o comentário é para o post',
  `text` varchar(400) NOT NULL,
  `visibilidade` int(11) DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `comment`
--

INSERT INTO `comment` (`id`, `user`, `id_post`, `id_reply`, `text`, `visibilidade`, `data_hora`) VALUES
(3, 'teste4', 11, NULL, 'Teste Comentario', 8, '2022-07-05 11:13:06'),
(4, 'teste4', 11, 3, 'Teste Resposta de comentario', 1, '2022-07-05 11:14:17'),
(5, 'teste4', 11, NULL, 'Teste de Comentario 2', 1, '2022-07-05 11:14:17'),
(6, 'teste4', 11, 5, 'teste de resposta 2', 1, '2022-07-05 11:50:59'),
(7, 'teste4', 11, NULL, 'teste', 1, '2022-07-05 18:44:04'),
(8, 'teste4', 11, NULL, 'teste2', 1, '2022-07-05 20:54:54'),
(9, 'teste4', 11, 4, 'Teste Resposta de Resposta', 1, '2022-07-05 21:57:00'),
(10, 'teste4', 11, 6, 'teste de resposta de resposta 2', 1, '2022-07-05 21:58:03'),
(11, 'teste4', 11, 8, 'teste de resposta', 1, '2022-07-05 21:59:40'),
(12, 'teste4', 11, 7, 'resposta', 1, '2022-07-05 22:08:45'),
(13, 'teste4', 11, 3, 'Teste Resposta', 1, '2022-07-06 12:52:09'),
(14, 'teste4', 11, 8, 'Teste de Resposta 2', 1, '2022-07-11 14:49:59'),
(24, 'teste4', 23, NULL, 'Teste', 1, '2022-07-15 19:57:03'),
(27, 'teste4', 23, NULL, 'Teste', 1, '2022-07-15 20:01:17'),
(28, 'teste4', 23, 27, 'Teste de Exclusão', 1, '2022-07-15 20:02:17'),
(31, NULL, 25, NULL, 'Teste de exclusão de perfil', 8, '2022-07-16 08:39:13'),
(32, NULL, 25, 31, 'Teste de exclusão de perfil', 8, '2022-07-16 08:39:36'),
(33, NULL, 26, NULL, 'Teste de exclusão de perfil', 8, '2022-07-16 08:54:29'),
(34, NULL, 25, NULL, 'Testes de exclusão', 8, '2022-07-16 10:02:38'),
(35, NULL, 27, NULL, 'Teste', 8, '2022-07-17 15:05:35'),
(36, NULL, 27, 35, 'Teste de Resposta', 8, '2022-07-17 15:10:52'),
(37, 'teste4', 27, 35, 'Teste', 1, '2022-07-17 16:24:13');

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
-- Extraindo dados da tabela `comment_like`
--

INSERT INTO `comment_like` (`id_comment`, `user`, `vote`, `data_hora`) VALUES
(4, 'teste4', 1, '2022-07-16 19:29:41'),
(8, 'teste4', 1, '2022-07-16 19:29:51'),
(11, 'teste4', 1, '2022-07-16 19:29:54'),
(14, 'teste4', -1, '2022-07-16 19:29:53');

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
(3, 0, '2022-07-05 11:13:06'),
(4, 1, '2022-07-16 19:29:41'),
(5, 0, '2022-07-05 11:14:17'),
(6, 0, '2022-07-05 11:50:59'),
(7, 0, '2022-07-05 18:44:04'),
(8, 1, '2022-07-16 19:29:51'),
(9, 0, '2022-07-15 19:51:26'),
(10, 0, '2022-07-05 21:58:03'),
(11, 1, '2022-07-16 19:29:54'),
(12, 0, '2022-07-05 22:08:45'),
(13, 0, '2022-07-06 12:52:09'),
(14, -1, '2022-07-16 19:29:53'),
(24, 0, '2022-07-15 19:57:03'),
(27, 0, '2022-07-15 20:01:17'),
(28, 0, '2022-07-15 20:02:17'),
(31, 0, '2022-07-16 08:39:13'),
(32, 0, '2022-07-16 08:39:36'),
(33, 0, '2022-07-16 08:54:29'),
(34, 0, '2022-07-16 10:02:38'),
(35, -1, '2022-07-17 15:28:16'),
(36, 1, '2022-07-17 15:28:18'),
(37, 0, '2022-07-17 16:24:13');

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
(2, 1, '2022-07-04 21:36:29'),
(4, 1, '2022-07-04 15:46:18'),
(5, 1, '2022-07-04 15:46:20'),
(6, 1, '2022-07-04 15:46:21'),
(10, 1, '2022-07-04 15:46:23'),
(11, 1, '2022-07-15 19:51:39'),
(13, 1, '2022-07-06 21:14:39'),
(17, 1, '2022-07-11 17:35:13'),
(18, 0, '2022-07-15 19:45:27'),
(19, 0, '2022-07-11 17:35:10'),
(20, 0, '2022-07-11 18:07:39'),
(21, 0, '2022-07-13 10:28:40'),
(22, 1, '2022-07-15 19:50:54'),
(23, 0, '2022-07-15 20:30:29'),
(25, 0, '2022-07-16 08:38:39'),
(26, 0, '2022-07-16 08:49:49'),
(27, 0, '2022-07-17 15:11:38');

-- --------------------------------------------------------

--
-- Estrutura da tabela `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `post` varchar(3000) NOT NULL,
  `visibilidade` int(11) DEFAULT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `post`
--

INSERT INTO `post` (`id`, `username`, `titulo`, `post`, `visibilidade`, `data_hora`) VALUES
(2, 'teste4', 'Teste', 'easrdjfkhvb jnklmç', 1, '2021-12-04 15:37:24'),
(4, 'teste5', NULL, 'egsbhsrndfs\\r\\nxfncgnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn\\r\\ncgncgncnc\\r\\ncgngggggggggggggggggggggggggggggggggggggggggggggggg', 1, '2021-12-04 16:39:08'),
(5, 'teste5', NULL, 'wehersehw\\r\\nsehdrhzdr\\r\\nsrhdb', 1, '2021-12-04 16:44:24'),
(6, 'teste5', NULL, 'testando criar novas publicações', 1, '2021-12-04 16:46:29'),
(10, 'teste5', NULL, 'fghj\\r\\ncvbnm', 1, '2021-12-04 17:00:19'),
(11, 'teste5', 'Teste', 'lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. lectus sit amet est placerat. consectetur adipiscing elit ut aliquam purus. aliquam purus sit amet luctus venenatis.\\r\\n\\r\\n quam id leo in vitae turpis massa sed elementum tempus. morbi quis commodo odio aenean sed adipiscing. at varius vel pharetra vel. bibendum at varius vel pharetra. tristique senectus et netus et malesuada. sagittis eu volutpat odio facilisis. sed vulputate mi sit amet mauris commodo. pellentesque habitant morbi tristique senectus et netus. cursus mattis molestie a iaculis at.\\r\\n\\r\\nmassa sapien faucibus et molestie ac feugiat sed lectus. non blandit massa enim nec dui nunc mattis enim ut. ipsum a arcu cursus vitae congue mauris. morbi tristique senectus et netus. quisque sagittis purus sit amet volutpat consequat mauris nunc. elit pellentesque habitant morbi tristique senectus et netus et. tellus in hac habitasse platea dictumst vestibulum rhoncus est. turpis nunc eget lorem dolor sed viverra. morbi tincidunt augue interdum velit euismod. leo vel fringilla est ullamcorper eget nulla facilisi etiam dignissim. condimentum id venenatis a condimentum vitae sapien. cursus in hac habitasse platea dictumst quisque sagittis. enim facilisis gravida neque convallis a cras semper auctor neque. sed nisi lacus sed viverra.', 1, '2021-12-04 17:11:08'),
(13, 'teste15', NULL, 'ruhdrhdztgnfgdndndfndf', 1, '2021-12-11 19:42:36'),
(17, 'teste4', NULL, 'teste de votos', 1, '2022-07-05 08:45:22'),
(18, 'teste4', NULL, 'teste de redirecionamento', 1, '2022-07-07 20:49:56'),
(19, 'teste4', NULL, 'teste', 1, '2022-07-07 20:50:13'),
(20, 'teste4', 'teste', 'teste de categoria', 1, '2022-07-11 18:07:39'),
(21, 'teste4', '', 'teste de sub-categoria', 1, '2022-07-13 10:28:40'),
(22, 'teste4', 'teste', 'teste de categoria', 1, '2022-07-13 10:29:23'),
(23, 'teste4', 'Teste', 'Teste', 8, '2022-07-15 19:49:54'),
(25, NULL, 'Teste', 'Teste de exclusão de perfil', 8, '2022-07-16 08:38:39'),
(26, NULL, 'Teste', 'Teste de exclusão de perfil', 8, '2022-07-16 08:49:49'),
(27, NULL, 'Teste', 'Teste', 8, '2022-07-17 15:05:03');

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

--
-- Extraindo dados da tabela `post_categoria`
--

INSERT INTO `post_categoria` (`id_post`, `id_categoria`) VALUES
(2, 2),
(4, 2),
(5, 2),
(6, 2),
(10, 2),
(11, 2),
(13, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 3),
(22, 2),
(23, 3),
(25, 2),
(26, 3),
(27, 4);

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
(2, 'teste4', 1, '2022-07-04 21:36:29'),
(4, 'teste4', 1, '2022-07-04 15:46:18'),
(5, 'teste4', 1, '2022-07-04 15:46:20'),
(6, 'teste4', 1, '2022-07-04 15:46:21'),
(10, 'teste4', 1, '2022-07-04 15:46:23'),
(11, 'teste4', 1, '2022-07-15 19:51:39'),
(13, 'teste4', 1, '2022-07-06 21:14:39'),
(17, 'teste4', 1, '2022-07-11 17:35:13'),
(22, 'teste4', 1, '2022-07-15 19:50:54');

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
(1, 'Masculino'),
(2, 'Feminino'),
(3, 'Não Binário'),
(4, 'N/A');

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
(2, 'brasileiro'),
(3, 'espanhol'),
(4, 'frances'),
(5, 'italiano'),
(6, 'alemão'),
(7, 'chinês');

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
(6, 'Moderadores'),
(7, 'Administradores'),
(8, 'deletado'),
(9, 'Banido');

-- --------------------------------------------------------

--
-- Estrutura da tabela `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `post_reported` int(11) DEFAULT NULL,
  `comment_reported` int(11) DEFAULT NULL,
  `user_reported` varchar(100) DEFAULT NULL,
  `motivo` int(11) NOT NULL,
  `outro` varchar(500) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `report`
--

INSERT INTO `report` (`id`, `user`, `post_reported`, `comment_reported`, `user_reported`, `motivo`, `outro`, `status`, `data_hora`) VALUES
(1, 'teste4', 23, NULL, NULL, 1, '', 2, '2022-07-16 18:20:03'),
(2, 'teste4', 23, NULL, NULL, 1, '', 1, '2022-07-16 18:16:42'),
(3, 'teste4', 22, NULL, NULL, 7, 'Testes de motivo - reporte', 1, '2022-07-16 18:16:44'),
(4, 'teste4', NULL, 27, NULL, 1, '', 1, '2022-07-16 18:16:45'),
(5, 'teste4', NULL, NULL, 'teste5', 1, '', 1, '2022-07-16 18:16:46');

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
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`username`, `email`, `password`, `nivel`, `status`, `token`, `marketing`, `data_hora`) VALUES
('teste15', 'teste15@teste.com', '$2y$10$5SdV2azzCpgAOPjx2cUZAePesPmwAzkaOMoP3oAS76pWkSk9CPt6C', 1, 2, '', 2, '2021-12-11 17:47:16'),
('teste16', 'teste16@teste.com', '$2y$10$1CzSkvRY6HedkcOnffQLDOS6rRevyEgXjVJJk0VUE5ibyGVmVGi6O', 1, 2, '', 2, '2022-03-03 10:58:31'),
('teste17', 'teste17@teste.com', '$2y$10$rqBlEBVHc.QioUfkQ7FD8OeM88X6X/I2M1ZeaGXF2DWeeKD/hq2z6', 1, 2, '', 1, '2022-03-03 11:11:23'),
('teste4', 'teste4@teste.com', '$2y$10$b3rIV8XqCUg65otX6xbW4eayZH6AwOgr3pAKpq7wT07fVkOelfcKK', 3, 2, '', 1, '2021-11-29 19:40:52'),
('teste5', 'teste5@teste.com', '$2y$10$mjB2jpwH1JGmLkb486QTs.VEwLsYwrxwfJ3dyB78zeVIypPpVSGnK', 1, 2, '', 2, '2021-11-28 14:37:12');

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
DELIMITER $$
CREATE TRIGGER `reset_user_comment_after_delete` BEFORE DELETE ON `user` FOR EACH ROW update comment c
SET c.visibilidade = 8
where c.user = old.username
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `reset_user_post_after_delete` BEFORE DELETE ON `user` FOR EACH ROW update post p
SET p.visibilidade = 8
where p.username = old.username
$$
DELIMITER ;

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
('teste15', 'teste15', NULL, NULL, NULL, '', 'imagens/teste15/1658078250.webp', NULL, 2),
('teste16', 'teste16', NULL, NULL, NULL, NULL, 'imagens/teste16/defaultPic.png', NULL, 2),
('teste17', 'teste17', NULL, NULL, NULL, NULL, 'imagens/teste17/defaultPic.png', NULL, 2),
('teste4', 'teste4', NULL, 2, 1, NULL, 'imagens/teste4/1658078024.webp', NULL, 1),
('teste5', 'teste5', NULL, 1, 2, NULL, 'imagens/teste5/defaultPic.png', NULL, 1);

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
  ADD KEY `comment_reply` (`id_reply`),
  ADD KEY `comment_visibilidade` (`visibilidade`);

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
  ADD KEY `report_motivo` (`motivo`),
  ADD KEY `report_user_reported` (`user_reported`) USING BTREE,
  ADD KEY `report_user` (`user`);

--
-- Índices para tabela `report_motivo`
--
ALTER TABLE `report_motivo`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_nivel` (`nivel`),
  ADD KEY `fk_status` (`status`),
  ADD KEY `fk_marketing` (`marketing`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `profile_genero`
--
ALTER TABLE `profile_genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `profile_nacionalidade`
--
ALTER TABLE `profile_nacionalidade`
  MODIFY `id_nacionalidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `profile_visibility`
--
ALTER TABLE `profile_visibility`
  MODIFY `id_visibilidade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `report_motivo`
--
ALTER TABLE `report_motivo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `comment_user` FOREIGN KEY (`user`) REFERENCES `user` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_visibilidade` FOREIGN KEY (`visibilidade`) REFERENCES `profile_visibility` (`id_visibilidade`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_user-post` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `post_visibilidade` FOREIGN KEY (`visibilidade`) REFERENCES `profile_visibility` (`id_visibilidade`);

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
  ADD CONSTRAINT `report_user` FOREIGN KEY (`user`) REFERENCES `user` (`username`) ON DELETE CASCADE,
  ADD CONSTRAINT `report_user_reported` FOREIGN KEY (`user_reported`) REFERENCES `user` (`username`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `user`
--
ALTER TABLE `user`
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
