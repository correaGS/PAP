<?php
	session_start();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    error_reporting(E_ALL);
    include_once  'conexaobasedados.php'; 
    include_once 'comandosbasedados.php';
    
        $erro = "";
        $tipo = $_POST["tipo_reporte"];
        $report_id = $_POST["report_id"];
        $id_reportado = $_POST["id_reportado"];
        $report_status = $_POST["report_status"];

        if ( $report_status == "permitido" ) {
    
            $report_id = $_POST["report_id"];
            
            $sql= concluir_reporte();

            if($stmt = mysqli_prepare($_conn, $sql)){

                $stmt -> bind_param('i', $report_id);
                $stmt -> execute();
                $stmt->free_result();
                $stmt->close();
      
              }else{
                      
              $erro = "STATUS ADMIN (inserir post): " . mysqli_error($_conn);
            }

            
            
            
            
        } else if( $report_status == "negado" ){
            
            $report_id = $_POST["report_id"];
            $id_reportado = $_POST["id_reportado"];
            $tipo_reporte = $_POST["tipo_reporte"];
            
            $sql = concluir_reporte();
            
            if ( $stmt = mysqli_prepare($_conn, $sql) ) {

                $stmt -> bind_param('i', $report_id);
                $stmt -> execute();
                $stmt->free_result();
                $stmt->close();

                $sql_negar = negar_reporte($tipo_reporte,$id_reportado);
            
                if ( $stmt_negar = mysqli_prepare($_conn, $sql_negar) ) {

                    $stmt_negar -> bind_param('i', $id_reportado);
                    $stmt_negar -> execute();
                    $stmt_negar ->free_result();
                    $stmt_negar ->close();
                    
                } else {
    
                    $erro = "STATUS ADMIN (apagar post): " . mysqli_error($_conn);
                }
                
            } else {
        
                $stmt->close();
                // falhou a atualização
                
                $erro = "STATUS ADMIN (apagar post): " . mysqli_error($_conn);
            }
                
        }

    $dados = array(
        'erro' => $erro
    );

    echo json_encode($dados);
?>