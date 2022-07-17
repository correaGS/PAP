<?php
	session_start();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    error_reporting(E_ALL);
    include_once  'conexaobasedados.php'; 
    include_once 'comandosbasedados.php';

    $erro = "";
    $user_comentario = "";
    $id_post = $_POST["id_post"];
    $conteudo_comentario = "";
    $reply = $_POST["id_comentario"];

    if($reply == 0){
        $reply = NULL;
    }

    if(empty($_POST["conteudo_comentario"])){

        $erro .= "<p class='text-danger'>Comentário é obrigatorio</p>";

    }else{

        $conteudo_comentario = $_POST["conteudo_comentario"];

    }

    if(empty($_SESSION["UTILIZADOR"])){

        $erro .= "<p class='text-danger'>É preciso estar Logado</p>";

    }else{

        $user_comentario = $_SESSION["UTILIZADOR"];

    }

    if($erro == ""){
        
        $sql = adicionar_comentario();
        $stmt = $_conn->prepare($sql);
        $stmt->bind_param('siis', $user_comentario, $id_post, $reply, $conteudo_comentario);
        $stmt->execute();

    }

    $dados = array(
        'erro' => $erro
    );

    echo json_encode($dados);
?>