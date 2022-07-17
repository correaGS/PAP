<?php

// para fazer a conexão é preciso: hostname, utilizador da bd, senha, nome da bd
// base de dados -> id19254273_basedados
// user -> id19254273_gabriel_correa
// Passe -> oY->&pYxvmV5auKq

$_conn=mysqli_connect("localhost","root","","basedados");
$_conn->set_charset('utf8');

// Verificar se a conexão correu bem
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


