<?php
//FUNÇÕES RETORNAM QUERY COM O COMANDO A SER UTILIZADO NA BASE DE DADOS
//Visualizar a quantidade de utilizadores registados 
function qtdUsers() {
    return $query="SELECT COUNT(username) AS total
    FROM user;";
   }
   
   //Verifica se o utilizador existe
   function userVerification() {
       return $query="SELECT user.username, user.email, user_profile.nome, user.password, user_profile.genero, user_profile.nacionalidade, user_profile.foto, user_profile.bio, user_profile.visibilidade, user.nivel, user.status, user.token, user.marketing, user.data_hora  
       FROM user_profile
       right JOIN user
       ON user_profile.username = user.username
       Where user.username = ? OR user.email = ?;";
      }
   
   // Altera Token de um utilizador
      function updateToken() {
       return $query="UPDATE user SET token = ? WHERE username = ?;";
      }
   
   // Altera Password de um utilizador
   function updatePassword() {
       return $query="UPDATE user SET PASSWORD = ?, TOKEN = ''  WHERE username = ?;";
      }
   
   // Verifica se o utilizador com determinado token existe 
   function recuperarSenha() {
       return $query="SELECT user.username, user.email, user_profile.nome, user.password, user_profile.apelido, user_profile.telemovel, user_profile.foto, user_profile.visibilidade, user.nivel, user.status, user.token, user.marketing, user.data_hora  
       FROM user_profile
       right JOIN user
       ON user_profile.username = user.username
       WHERE user.username = ? AND user.token= ?;";
      }
   
   // Visualiza todos os utilizadores de forma descendente ao username  
   function pesquisaUserDESC() {
       return $query="SELECT user.username, user.email, user_profile.nome, user_profile.visibilidade, user.password, user.nivel, user.status, user.marketing, user.data_hora  
       FROM user_profile
       right JOIN user
       ON user_profile.username = user.username
       ORDER BY user.username DESC;";
      }
   
   // Visualiza todos os utilizadores de forma ascendente ao username  
   function pesquisaUserASC() {
       return $query="SELECT user.username, user.email, user_profile.nome, user_profile.visibilidade, user.password, user.nivel, user.status, user.marketing, user.data_hora  
       FROM user_profile
       right JOIN user
       ON user_profile.username = user.username
       ORDER BY user.username ASC;";
      }
   
   // Visualiza todos os utilizadores de forma descendente ao nome  
   function pesquisaNomeDESC() {
       return $query="SELECT user.username, user.email, user_profile.nome, user_profile.visibilidade, user.password, user.nivel, user.status, user.marketing, user.data_hora  
       FROM user_profile
       right JOIN user
       ON user_profile.username = user.username
       ORDER BY user_profile.nome DESC;";
      }
   
   // Visualiza todos os utilizadores de forma ascendente ao nome  
   function pesquisaNomeASC() {
       return $query="SELECT user.username, user.email, user_profile.nome, user_profile.visibilidade, user.password, user.nivel, user.status, user.marketing, user.data_hora  
       FROM user_profile
       right JOIN user
       ON user_profile.username = user.username
       ORDER BY user_profile.nome ASC;";
      }
   
   // Visualiza todos os utilizadores que se encaixam na pesquisa 
   function pesquisa(&$campoPesquisa) {
       return $query="SELECT user.username, user.email, user_profile.nome, user_profile.visibilidade, user.password, user.nivel, user.status, user.marketing, user.data_hora  
       FROM user_profile
       right JOIN user
       ON user_profile.username = user.username
       WHERE (user.username LIKE'%$campoPesquisa%') OR (user_profile.nome LIKE '%$campoPesquisa%') OR (user.email LIKE '%$campoPesquisa%') OR (user.data_hora LIKE '%$campoPesquisa%')
       ORDER BY user.username;";
      }
   
   // Deleta a conta de um utilizador
   function deletarConta() {
       return $query="DELETE from user where username = ?;";
      }
   
   // Cria uma conta
   function criarConta() {
       return $query="INSERT into user(username, email, password, nivel, status, marketing)
       VALUES(?, ?, ?, 1, 1, ?);";
      }
   function updateProfile() {
      return $query="UPDATE user_profile
      SET nome = ?,
      foto = ?
      where username = ?;";
      }
   
   // Bloqueia a conta de um utilizador
   function bloquearConta() {
       return $query="UPDATE user SET STATUS=3, TOKEN='' WHERE username=?;";
      }
   
   // Ativa a conta de um utilizador
   function ativarConta() {
       return $query="UPDATE user SET token ='', status=2 WHERE username=?;";
      }
   
   // Altera o perfil de um utilizador
   function alterarPerfil() {
       return $query="UPDATE user_profile
       SET
       nome = ?,
       visibilidade = ?,
       bio = ?,
       genero = ?,
       nacionalidade = ?
       WHERE username = ?;";
      }
      function alterarMarketing() {
         return $query="UPDATE user
         SET
         marketing = ?
         WHERE username = ?;";
        }

// Visualiza todos os utilizadores públicos de forma ascendente ao username 
function pesquisaUserPublico() {
   return $query="SELECT username
   FROM user_profile
   WHERE visibilidade = 1 AND username <>'".$_SESSION['UTILIZADOR']."'
   ORDER BY username ASC;";
  }

// Visualiza todos os utilizadores que se encaixam na pesquisa 
function pesquisaPublico(&$campoPesquisa) {
   return $query="SELECT username
   FROM user_profile
   WHERE visibilidade = 1 AND username like '%$campoPesquisa%'
   ORDER BY username ASC;";
  }

// Visualiza o perfil 
function verPerfil() {
   return $query="SELECT profile.username, profile.nome, profile.foto, profile.bio, profile.visibilidade, genero.descricao as genero, nacionalidade.descricao as nacionalidade
   FROM user_profile as profile
   left Join profile_genero genero ON genero.id_genero = profile.genero 
   left Join profile_nacionalidade nacionalidade ON nacionalidade.id_nacionalidade = profile.nacionalidade
   WHERE profile.visibilidade = 1 AND profile.username = ?;";
  }

// Insere Post 
function postar() {
    return $query="INSERT INTO post (username, post, titulo, visibilidade)
    VALUES(?,?,?,1);";
   }

// Visualiza a quantidade de post  
function countPosts() {
    return $query="SELECT COUNT(post) AS total
    FROM post
    where visibilidade = 1;";
   }

// Ver Posts
function verPosts(&$offset, &$pagePosts) {
   return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1
      ORDER BY data_hora DESC
      LIMIT $offset, $pagePosts;";
}

//Visualizar publicação por id
function post() {
   return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      left Join user_profile ON post.username = user_profile.username
      WHERE post.id = ?;";
}

// Ver Post de um Utilizador
function verPostsUtilizador(&$username) {
   return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
   Join post_categoria ON post_categoria.id_post = post.id 
   Join categoria ON categoria.id = post_categoria.id_categoria
   Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
   Join user_profile ON post.username = user_profile.username
   WHERE post.username = '$username' AND post.visibilidade = 1
   ORDER BY data_hora DESC;";
  }

// Apagar Post de um Utilizador
function apagarPost() {
   return $query="UPDATE post SET visibilidade = 8 WHERE id = ?;";
  }

// Envia dados para o console
function console_error($erro) {
   $output = $erro;
   echo "<script>console.log($output);</script>";
}

function countVotes($id_post) {
   return $query="SELECT *
   FROM count_like_post WHERE id_post = '$id_post'";
}

function like_post() {
   return $query="REPLACE INTO post_like SET vote = 1, id_post = ?, user = ?;";
}

function dislike_post() {
   return $query="REPLACE INTO post_like SET vote = -1, id_post = ?, user = ?;";
}

function remove_like_post() {
   return $query="DELETE FROM post_like WHERE id_post = ? AND user = ?;";
}

function ja_votou_post() {
   return $query="SELECT * FROM post_like WHERE post_like.id_post = ? AND post_like.user = ?;";
}

function ver_comentarios() {
   return $query="SELECT comment.*, user_profile.foto  FROM comment 
   left Join user_profile ON comment.user = user_profile.username
   WHERE comment.id_post = ? AND comment.id_reply IS NULL ORDER BY id DESC;";
}

// Ver Comentarios de um Utilizador
function verComentariosUtilizador(&$username) {
   return $query="SELECT comentario.*, reply.user as user_reply, user_profile.foto FROM `comment` comentario 
   Left Join comment reply ON reply.id = comentario.id_reply 
   Join user_profile ON comentario.user = user_profile.username
   WHERE comentario.user = '$username' AND comentario.visibilidade = 1;";
}

function ver_respostas() {
   return $query="SELECT comentario.*, reply.user as user_reply, user_profile.foto FROM `comment` comentario 
                  Left Join comment reply ON reply.id = comentario.id_reply 
                  left Join user_profile ON comentario.user = user_profile.username
                  WHERE comentario.id_post = ? AND comentario.id_reply = ?; ";
}

function adicionar_comentario() {
   return $query="INSERT INTO comment (user, id_post, id_reply, text, visibilidade) VALUES(?, ?, ?, ?, 1);";
}

function countVotes_comentario($id_comentario) {
   return $query="SELECT *
   FROM count_like_comment WHERE id_comment = '$id_comentario'";
}

function like_comentario() {
   return $query="REPLACE INTO comment_like SET vote = 1, id_comment = ?, user = ?;";
}

function dislike_comentario() {
   return $query="REPLACE INTO comment_like SET vote = -1, id_comment = ?, user = ?;";
}

function remove_like_comentario() {
   return $query="DELETE FROM comment_like WHERE id_comment = ? AND user = ?;";
}

function ja_votou_comentario() {
   return $query="SELECT * FROM comment_like WHERE comment_like.id_comment = ? AND comment_like.user = ?;";
}

function get_categoria() {
   return $query="SELECT * FROM categoria where categoria.parent_id is NULL;";
}

function get_subcategoria($id_categoria) {
   return $query="SELECT * FROM categoria where categoria.parent_id = $id_categoria;";
}

function post_categoria() {
   return $query="INSERT INTO post_categoria (id_post, id_categoria) VALUES(?, ?);";
}

function pesquisa_posts_simples($pesquisa_geral) {
   
   return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
   Join post_categoria ON post_categoria.id_post = post.id 
   Join categoria ON categoria.id = post_categoria.id_categoria
   Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
   Join user_profile ON post.username = user_profile.username
   WHERE post.visibilidade = 1 AND LOWER(post.post) like LOWER('%$pesquisa_geral%') OR LOWER(post.titulo) like LOWER('%$pesquisa_geral%')
   ORDER BY post.data_hora DESC;";
}

function pesquisa_posts_advanced($pesquisa_post, $pesquisa_titulo, $id_categoria) {

   //Pesquisa somente pelo texto do post da publicação
   if(!empty($pesquisa_post) && empty($pesquisa_titulo) && empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND LOWER(post.post) like LOWER('%$pesquisa_post%')
      ORDER BY post.data_hora DESC;";
   } 

   //Pesquisa somente pelo texto do titulo da publicação
   else if(empty($pesquisa_post) && !empty($pesquisa_titulo) && empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND LOWER(post.titulo) like LOWER('%$pesquisa_titulo%')
      ORDER BY post.data_hora DESC;";
   }

   //Pesquisa somente pela categoria da publicação
   else if(empty($pesquisa_post) && empty($pesquisa_titulo) && !empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND (categoria.id = $id_categoria OR categoria.parent_id = $id_categoria)
      ORDER BY post.data_hora DESC;";
   }

   //Pesquisa pelo texto do post e do titulo da publicação
   else if(!empty($pesquisa_post) && !empty($pesquisa_titulo) && empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND LOWER(post.post) like LOWER('%$pesquisa_post%') AND post.titulo like LOWER('%$pesquisa_titulo%')
      ORDER BY post.data_hora DESC;";
   }

   //Pesquisa pelo texto do post e pela categoria da publicação
   else if(!empty($pesquisa_post) && empty($pesquisa_titulo) && !empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND LOWER(post.post) like LOWER('%$pesquisa_post%') AND (categoria.id = $id_categoria OR categoria.parent_id = $id_categoria)
      ORDER BY post.data_hora DESC;";
   }

   //Pesquisa pelo texto do titulo e pela categoria da publicação
   else if(empty($pesquisa_post) && !empty($pesquisa_titulo) && !empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND LOWER(post.titulo) like LOWER('%$pesquisa_titulo%') AND (categoria.id = $id_categoria OR categoria.parent_id = $id_categoria)
      ORDER BY post.data_hora DESC;";
   }

   //Pesquisa por publicações com todas as condições
   else if(!empty($pesquisa_post) && !empty($pesquisa_titulo) && !empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND LOWER(post.post) like LOWER('%$pesquisa_post%') AND LOWER(post.titulo) like LOWER('%$pesquisa_titulo%') AND (categoria.id = $id_categoria OR categoria.parent_id = $id_categoria)
      ORDER BY post.data_hora DESC;";
   }

   else if(empty($pesquisa_post) && empty($pesquisa_titulo) && empty($id_categoria)){
      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      Join user_profile ON post.username = user_profile.username
      WHERE post.visibilidade = 1 AND LOWER(post.post) = NULL AND LOWER(post.titulo) = NULL AND (categoria.id = NULL OR categoria.parent_id = NULL)
      ORDER BY post.data_hora DESC;";
   }
}

function update_profile_picture() {
   return $query="UPDATE user_profile
   SET foto = ?
   where username = ?;";
}

function get_genero() {
   return $query="SELECT * from profile_genero;";
}

function get_nacionalidade() {
   return $query="SELECT * from profile_nacionalidade;";
}


function get_motivo() {
   return $query="SELECT * FROM report_motivo;";
}

function reportar($tipo, $id) {
   if($tipo == "post"){

      return $query="INSERT INTO report (user, post_reported, motivo, outro, status) VALUES (?, ". $id .", ?, ?, 1)";

   } else if($tipo == "comentario"){

      return $query="INSERT INTO report (user, comment_reported, motivo, outro, status) VALUES (?, ". $id .", ?, ?, 1)";
      
   } else if($tipo == "user"){

      return $query="INSERT INTO report (user, user_reported, motivo, outro, status) VALUES (?, '". $id ."', ?, ?, 1)";

   }
}

function ver_reportes() {
   return $query="SELECT report.*, motivo.descricao as motivo_desc FROM report 
   JOIN report_motivo motivo ON motivo.id = report.motivo
   where status = 1
   ORDER BY data_hora DESC;";
}

function ver_conteudo_reporte($tipo, $id) {
   if($tipo == "publicação"){

      return $query="SELECT post.*, categoria.descricao as post_categoria, parent_categoria.descricao as main_categoria, user_profile.foto FROM `post` 
      Join post_categoria ON post_categoria.id_post = post.id 
      Join categoria ON categoria.id = post_categoria.id_categoria
      Left Join categoria parent_categoria ON parent_categoria.id = categoria.parent_id
      left Join user_profile ON post.username = user_profile.username
      WHERE post.id = $id;";

      } else if ($tipo == "comentário"){

         return $query="SELECT comentario.*, reply.user as user_reply FROM `comment` comentario 
         Left Join comment reply ON reply.id = comentario.id_reply WHERE comentario.id = $id; ";

      } else if($tipo == "user"){
         return $query="SELECT profile.username, profile.nome, profile.foto, profile.bio, profile.visibilidade, genero.descricao as genero, nacionalidade.descricao as nacionalidade
         FROM user_profile as profile
         left Join profile_genero genero ON genero.id_genero = profile.genero 
         left Join profile_nacionalidade nacionalidade ON nacionalidade.id_nacionalidade = profile.nacionalidade
         where profile.username = '$id';";
      }
}

function concluir_reporte() {
   return $query="UPDATE `report` SET `status` = 2 WHERE `report`.`id` = ?;";
}

function negar_reporte($tipo,$username) {
   if($tipo == "publicação"){

      return $query="UPDATE post
      SET visibilidade = 8
      WHERE id = ?;";

      } else if ($tipo == "comentário"){

         return $query="UPDATE comment
         SET visibilidade = 8
         WHERE id = ?;";

      } else if($tipo == "user"){
         return $query="UPDATE user_profile
         SET foto = 'imagens/$username/defaultPic.png',
         bio = NULL
         WHERE username = ?;";
      }
}

function apagarComentario() {
   return $query="UPDATE comment SET visibilidade = 8 WHERE id = ?;";
  }

function substr_close_tags($code, $limit = 500)
{
    if ( strlen($code) <= $limit )
    {
        return $code;
    }

    $html = substr($code, 0, $limit);
    preg_match_all ( "#<([a-zA-Z]+)#", $html, $result );

    foreach($result[1] AS $key => $value)
    {
        if ( strtolower($value) == 'br' )
        {
            unset($result[1][$key]);
        }
    }
    $openedtags = $result[1];

    preg_match_all ( "#</([a-zA-Z]+)>#iU", $html, $result );
    $closedtags = $result[1];

    foreach($closedtags AS $key => $value)
    {
        if ( ($k = array_search($value, $openedtags)) === FALSE )
        {
            continue;
        }
        else
        {
            unset($openedtags[$k]);
        }
    }

    if ( empty($openedtags) )
    {
        if ( strpos($code, ' ', $limit) == $limit )
        {
            return $html."...";
        }
        else
        {
            return substr($code, 0, strpos($code, ' ', $limit))."...";
        }
    }

    $position = 0;
    $close_tag = '';
    foreach($openedtags AS $key => $value)
    {   
        $p = strpos($code, ('</'.$value.'>'), $limit);

        if ( $p === FALSE )
        {
            $code .= ('</'.$value.'>');
        }
        else if ( $p > $position )
        {
            $close_tag = '</'.$value.'>';
            $position = $p;
        }
    }

    if ( $position == 0 )
    {
        return $code;
    }

    return substr($code, 0, $position).$close_tag."...";
}
?>