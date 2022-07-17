<?php 

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

if ($_SESSION["NIVEL_UTILIZADOR"]!=3) {
    ?>
    <script>
        $("#botao-voltar").click(function (){
            if ('referrer' in document) {
                window.location = document.referrer;
            } else {
                window.history.back();
            }
        });
    </script>
    <?php
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Gerir Reportes</title>
<?php include_once "style.html";?>
</head>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center w3-container">
  <div class="center">
    <div class="w3-theme w3-padding-64 w3-padding-large w3-center">
      <h1>Gerir Reportes</h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">

        <?php
            $sql_reporte = ver_reportes();
            $resultadoTabela = mysqli_query($_conn, $sql_reporte);
            if (mysqli_num_rows($resultadoTabela) > 0){
                while($rowTabela = mysqli_fetch_array($resultadoTabela)) {
                    $id = $rowTabela["id"];
                    $user = $rowTabela["user"];
                    if(isset($rowTabela["post_reported"])){
                        $tipo_reporte = "publicação";
                        $id_reportado = $rowTabela["post_reported"];
                    } else if(isset($rowTabela["comment_reported"])){
                        $tipo_reporte = "comentário";
                        $id_reportado = $rowTabela["comment_reported"];
                    } else if(isset($rowTabela["user_reported"])){
                        $tipo_reporte = "user";
                        $id_reportado = $rowTabela["user_reported"];
                    }
                    if($rowTabela["motivo"] == 7){
                        $motivo = $rowTabela["outro"];
                        $motivo = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $motivo);
                        $motivo = wordwrap($motivo, 100);
                    }else{
                        $motivo = $rowTabela["motivo_desc"];
                    }
                    $data_hora = $rowTabela["data_hora"];

                    ?>
                        <div class="w3-padding-16 w3-center">
                        <div class="w3-section w3-panel w3-round-large w3-theme-l2 w3-card-4 w3-padding" style="max-width: 750px; margin: auto;">

                        <div class="w3-col w3-center" style="display: flex; justify-content:center; align-items:center; width: 50px;">

                            <?php
                    
                            ?>
                            
                            <div class="w3-padding">
                            <form  id="report_form_permitir" action="#" method="POST">
                                <div class="w3-padding">
                                    <button type="submit" class="btn w3-theme w3-hover-purple btn-lg">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </button>
                                </div>
                                <input class="hidden" value="permitido" name="report_status">
                                <input class="hidden" value="<?php echo $id?>" name="report_id">
                                <input class="hidden" value="<?php echo $id_reportado?>" name="id_reportado">
                                <input class="hidden" value="<?php echo $tipo_reporte?>" name="tipo_reporte">
                            </form>
                            <form  id="report_form_negar" action="#" method="POST">
                                <div class="w3-padding">
                                    <button type="submit"  class="btn w3-theme w3-hover-purple btn-lg">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                </div>
                                <input class="hidden" value="negado" name="report_status">
                                <input class="hidden" value="<?php echo $id?>" name="report_id">
                                <input class="hidden" value="<?php echo $id_reportado?>" name="id_reportado">
                                <input class="hidden" value="<?php echo $tipo_reporte?>" name="tipo_reporte">
                            </form>
                            </div>
                        </div>

                        <div class="w3-rest w3-padding">
                        <div class="w3-section w3-center">
                        <p><h4>Reporte de <?php echo $tipo_reporte;?></h4></p>
                        <p><?php echo "Por ".$user." • ". $data_hora; ?></p>
                        <p>Motivo: <?php echo $motivo?></p>
                        <div class="w3-cell"style="vertical-align: middle;"></div>
                        <div class="w3-cell"style="vertical-align: middle;"><b style="padding-left: 10px;">
                        
                        </b></div>
                        </div>
                        <div class="w3-section w3-center">
                        <p><h4>Conteudo de <?php echo $tipo_reporte;?></h4></p>

                            <?php
                                    $conteudo_reporte_sql = ver_conteudo_reporte($tipo_reporte, $id_reportado);
                                    $resultadoConteudo = mysqli_query($_conn, $conteudo_reporte_sql);
                                    if (mysqli_num_rows($resultadoConteudo) > 0){
                                        while($rowConteudo = mysqli_fetch_array($resultadoConteudo)) {
                                            if($tipo_reporte == "publicação"){

                                                $user_post = $rowConteudo['username'];
                                                $titulo = $rowConteudo['titulo'];
                                                $titulo = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $titulo);
                                                $titulo = wordwrap($titulo, 100, "<br>\n",TRUE);
                                                $post = $rowConteudo['post'];
                                                $post_categoria = $rowConteudo["post_categoria"];
                                                $main_categoria = $rowConteudo["main_categoria"];
                                                $post = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $post);
                                                $post = wordwrap($post, 100, "<br>\n",TRUE);
                                                $visibilidade_post = $rowConteudo['visibilidade'];
                                                $data_hora_post = $rowConteudo['data_hora'];

                                                if(empty($main_categoria)){
                                                    echo "<p>Por ".$user_post." • Em ". $post_categoria ." • ". $data_hora_post . "</p>";
                                                }else{
                                                    echo "<p>Por ".$user_post." • Em ". $main_categoria." - ". $post_categoria ." • ". $data_hora_post. "</p>";
                                                }

                                                ?>
                                                <p><h2 class="justificado text-width"><?php echo $titulo;?></h2></p>
                                                <p class="justificado text-width"><?php echo $post;?></p>
                                                <?php
                                                    

                                            }else if($tipo_reporte == "comentário"){

                                                $user_comentario = $rowConteudo["user"];
                                                $user_reply = $rowConteudo["user_reply"];
                                                $conteudo = $rowConteudo['text'];
                                                $conteudo = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $conteudo);
                                                $conteudo = wordwrap($conteudo, 100);
                                                $visibilidade_comentario = $rowConteudo['visibilidade'];
                                                $data_hora_comentario = $rowConteudo['data_hora'];

                                                if(!empty($user_reply)){
                                                    echo "<p><b>". $user_comentario ." em resposta a ". $user_reply ." • </b><i>". $data_hora_comentario ." </i></p>";
                                                }else{
                                                    echo "<p><b>". $user_comentario ." • </b><i>". $data_hora_comentario ." </i></p>";
                                                }

                                                ?>
                                                <p class="justificado"><?php echo $conteudo;?></p>
                                            
                                                <?php

                                            } else if($tipo_reporte == "user"){

                                                $nome = $rowConteudo['nome'];
                                                $pic = $rowConteudo['foto'];
                                                $nome = $rowConteudo['nome'];
                                                $bio = $rowConteudo['bio'];
                                                $bio = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $bio);
                                                $bio = wordwrap($bio, 100, "<br>\n",TRUE);
                                                $nacionalidade = $rowConteudo['nacionalidade'];
                                                $genero = $rowConteudo['genero'];

                                                ?>
                                                <div class="w3-section">
                                                <img src=<?php echo $pic;?> class="w3-image img-circle w3-center report-img">
                                                </div>
                                                <div class="w3-section">
                                                <p><label >Nome - <?php echo $nome;?></label></p>
                                                <p><label >Nacionalidade - <?php echo $nacionalidade;?></label></p>
                                                <p><label >Genero - <?php echo $genero;?></label></p>
                                                <p><label >Sobre<?php echo "<p class='text-width'>".$bio."</p>";?></label></p>
                                                </div>
                                                <?php

                                            }

                                        }
                                    }
                                
                                mysqli_free_result($resultadoConteudo);
                            ?>
                        
                        </div>
                        </div>
                        </div>
                        </div>
                    <?php
                }
              }
              mysqli_free_result($resultadoTabela);
        
        ?>

        <br><br>
        <form action="index.php" method="POST">
            <input type="submit" class="w3-btn w3-xlarge" value="Voltar">
        </form>
        </div>
    </div>
  </div>
</div>
    <?php include_once "footer.php";?>
</div>
</body>
</html>

<script>
  $(document).ready(function() {


    $(document).on('submit', '#report_form_permitir', function(event){
        if (!confirm('Clique em OK para PERMITIR')){
            event.preventDefault();
        } else {
            event.preventDefault();
            var dados_formulario = $(this).serialize();
            $.ajax({
            url:"validar_reporte.php",
            method:"POST",
            data: dados_formulario,
            dataType:"JSON",
            success:function(dados){
                if(dados.erro != ''){
                    alert("Erro ao concluir ação.");
                    return false
                    
                }else{
                    alert("Ação Concluida");
                    window.location.reload();
                }
            }
            })
        }
    });

    $(document).on('submit', '#report_form_negar', function(event){
        if (!confirm('Clique em OK para BANIR')){
            event.preventDefault();
        } else {
            event.preventDefault();
            var dados_formulario = $(this).serialize();
            $.ajax({
            url:"validar_reporte.php",
            method:"POST",
            data: dados_formulario,
            dataType:"JSON",
            success:function(dados){
                if(dados.erro != ''){
                    alert("Erro ao concluir ação.");
                    return false
                    
                }else{
                    alert("Ação Concluida");
                    window.location.reload();
                }
            }
            })
        }
    });

  });
</script>






