<?php
//FUNÇÕES RETORNAM QUERY COM O COMANDO A SER UTILIZADO NA BASE DE DADOS
//Visualizar a quantidade de utilizadores registados 
function qtdUsers() {
    return $query="SELECT COUNT(username) AS total
    FROM user;";
   }
   
   //Verifica se o utilizador existe
   function userVerification() {
       return $query="SELECT user.username, user.email, user_profile.nome, user.password, user_profile.apelido, user_profile.telemovel, user_profile.foto, user_profile.visibilidade, user.nivel, user.status, user.token, user.marketing, user.data_hora  
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
       WHERE user.username = _username AND user.token= _token;";
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
       apelido = ?,
       telemovel = ?,
       visibilidade = ?,
       foto = ?
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
   return $query="SELECT *
   FROM user_profile
   WHERE visibilidade = 1 AND username = ?;";
  }

// Insere Post 
function postar() {
    return $query="INSERT INTO post (username, post)
    VALUES(?,?);";
   }

// Visualiza a quantidade de post  
function countPosts() {
    return $query="SELECT COUNT(post) AS total
    FROM post;";
   }

// Ver Posts
function verPosts(&$offset, &$pagePosts) {
      return $query="SELECT post.username, post.post, post.data_hora, user_profile.foto
      FROM post
      left join user_profile
      on post.username = user_profile.username
      ORDER BY data_hora DESC
      LIMIT $offset, $pagePosts;";
   }

// Ver Post de um Utilizador
function verPostsUtilizador(&$username) {
   return $query="SELECT *
   FROM post WHERE username = '$username'
   ORDER BY data_hora DESC;";
  }

// Ver Post de um Utilizador
function apagarPost() {
   return $query="DELETE FROM post WHERE id = ?;";
  }

// Envia dados para o console
function console_error($erro) {
   $output = $erro;
   echo "<script>console.log($output);</script>";
}
?>