<?php
	session_start();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    error_reporting(E_ALL);
    include_once  'conexaobasedados.php'; 
    include_once 'comandosbasedados.php';
    
        $erro = "";
        $motivo = $_POST["motivo"];
        $tipo = $_POST["report"];
        $id = $_POST["id"];
        
        if(isset($_POST["outro_motivo"])){
          $outro_motivo = trim(mysqli_real_escape_string($_conn,$_POST["outro_motivo"]));
          $outro_motivo = trim($outro_motivo);
          $outro_motivo = strip_tags($outro_motivo);
        }else{ $outro_motivo = ""; }
  
          $codigo = $_SESSION["UTILIZADOR"];
          $sql = reportar($tipo, $id);
          if($stmt = mysqli_prepare($_conn, $sql)){
            $stmt->bind_param('sis', $codigo, $motivo, $outro_motivo);
            $stmt->execute();
            $id_post_criado = mysqli_insert_id($_conn);
            $stmt->free_result();
            $stmt->close();
  
          }else{
                  
          $erro = "STATUS ADMIN (inserir post): " . mysqli_error($_conn);
        }
      
    

    $dados = array(
        'erro' => $erro
    );

    echo json_encode($dados);
?>