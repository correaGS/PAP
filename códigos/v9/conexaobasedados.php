<?php

// para fazer a conexão é preciso: hostname, utilizador da bd, senha, nome da bd

$_conn=mysqli_connect("localhost","root","","basedados");
$_conn->set_charset('utf8');

// Verificar se a conexão correu bem
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


