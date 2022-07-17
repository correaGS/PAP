<?php 
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

$mensagem ="";
$postDisponivel = "SIM";

if ( isset($_POST['submit-voltar']) ) {
    
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}


    $id_post = $_GET['id']; // código do post...

    $sql = post();
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('i', $id_post);  
    $stmt->execute();

    $verPost = $stmt->get_result();
    
    if ($verPost->num_rows > 0) {
        while ($rowPost = $verPost->fetch_assoc()) {
            
            $username = $rowPost['username'];
            $titulo = $rowPost['titulo'];
            $post = $rowPost['post'];
            $post_categoria = $rowPost["post_categoria"];
            $main_categoria = $rowPost["main_categoria"];
            $pic = $rowPost["foto"];
            $post = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $post);
            $post = wordwrap($post, 100);
            $visibilidade = $rowPost['visibilidade'];
            $data_hora = $rowPost['data_hora'];
            mysqli_stmt_close($stmt);

            $votos = countVotes($id_post);
                $resultadoVotos = mysqli_query($_conn, $votos);
                if (mysqli_num_rows($resultadoVotos) > 0){
                  while($rowTabelaVotos = mysqli_fetch_array($resultadoVotos)) {
                  $total_votos_post = $rowTabelaVotos["likes"];
                  }
                }
                mysqli_free_result($resultadoVotos);

                $ja_votou = ja_votou_post();
                $stmt = $_conn->prepare($ja_votou);
                $stmt->bind_param('is', $id_post, $_SESSION['UTILIZADOR']);
                $stmt->execute();

                $resultado_ja_votou = $stmt->get_result();
                
                if ($resultado_ja_votou->num_rows > 0) {
                  while ($row_ja_votou = $resultado_ja_votou->fetch_assoc()) {
                      if ($row_ja_votou['vote']==1){
                        $voto_post="like";
                      } else if($row_ja_votou['vote']==-1){
                        $voto_post="dislike";
                      }
                  }
                } else {
                   $voto_post="nao";
                }
                
                $stmt->free_result();
                $stmt->close();
                 }
          }else {
            $postDisponivel = "NAO";
           $mensagem = "Post Indisponível";
        }

        if($visibilidade != 1){
          $postDisponivel = "NAO";
          $mensagem = "Post Indisponível";
        }
?>

<!DOCTYPE html>
<html>
<head>
<title>Post</title>
<?php include_once "style.html";?>
<style>
  .reply-box-1{
    margin-left:48px;
  }
  .reply-box-2{
    margin-left:96px;
  }
  .reply-box-3{
    margin-left:144px;
  }
  @media (min-width:481px) and (max-width: 768px){
    .reply-box-1{
      margin-left:24px;
    }
    .reply-box-2{
      margin-left:48px;
    }
    .reply-box-3{
      margin-left:72px;
    }
  }
  @media (max-width: 480px) {
    .reply-box-1{
      margin-left:10px;
    }
    .reply-box-2{
      margin-left:20px;
    }
    .reply-box-3{
      margin-left:30px;
    }
  }
</style>
</head>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center">
<div class="center">
      
      <div class="w3-container w3-theme-l5 w3-round-large w3-section">
           <div class="w3-padding-16">
           <div class="w3-panel w3-round-large w3-card-4 w3-padding">
            <div class="w3-section">
              <form action="reportar.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $id_post; ?>">
                <button  type="submit" name="report" value="post" class="btn w3-theme w3-hover-purple btn-sm" <?php if($visibilidade == 8){echo "disabled";}?>><i class="fa-solid fa-flag"></i></button>
              </form>
            <b class="w3-padding">
              
           <?php if($visibilidade == 8){
              $username = "[DELETADO]";
            }
            if(empty($main_categoria)){
            echo "Por ". $username." • Em ". $post_categoria ." • ". $data_hora;}
            else{echo "Por ".$username." • Em ". $main_categoria." - ". $post_categoria ." • ". $data_hora;}
            ?></b>
           <button type='button' class="btn <?php if($voto_post=="like"){echo "w3-purple";}else{echo"w3-theme";}?> w3-hover-purple btn-sm" voto='<?php echo $voto_post;?>' id='like_post_<?php echo $id_post;?>' onclick='like_post("<?php echo $id_post;?>")' value='Like' <?php if($visibilidade == 8){ echo "disabled";}?> ><i class="fa-solid fa-caret-up"></i></button>
           <b id='total_votos_post_<?php echo $id_post;?>'><?php echo $total_votos_post ?></b>
           <button type='button' class="btn <?php if($voto_post=="dislike"){echo "w3-purple";}else{echo"w3-theme";}?> w3-hover-purple btn-sm" voto='<?php echo $voto_post;?>' id='dislike_post_<?php echo $id_post;?>' onclick='dislike_post("<?php echo $id_post;?>")' value='dislike' <?php if($visibilidade == 8){ echo "disabled";}?> ><i class="fa-solid fa-caret-down"></i></button>
           
           </div>
           <div class="w3-section">
           <p><h2><?php if($visibilidade == 8){echo "[DELETADO]";}else echo $titulo;?></h2></p>
           <p class="justificado"><?php if($visibilidade == 8){echo "[DELETADO]";}else echo $post;?></p>
           </div>
           </div>
          </div>
          <div class="w3-padding-16">
           <div class="w3-section w3-panel w3-round-large w3-card-4 w3-padding ">
           <div class='panel panel-default'>
                <div class='panel-heading'> 
                  <p>Comentarios</p>
                </div>
                <form method="POST" id="formulario_comentario">
                  <div class='panel-body justificado'>
                 
                    <textarea name="conteudo_comentario" id="conteudo_comentario" class="w3-input" placeholder="Digite um Comentário..." rows="4"></textarea>
                    <input type="hidden" name="id_post" id="id_post" value="<?php echo $id_post;?>">
                    <input type="hidden" name="id_comentario" id="id_comentario" value="0">
                    
                  </div>
                  <div class='panel-footer' align='right'>
                    <input type="submit" class="btn btn-default" name="submeter" id="submeter" value="Submeter" <?php if($visibilidade == 8){ echo "disabled";}?>>
                  </div>
                </form>
                
            </div>
            <button type="button" class="btn btn-default w3-padding" style="margin: 25px;" name="atualizar" id="atualizar" >Atualizar</button>
            <span id="mensagem_comentario"></span>
              <br>
            <div id="mostrar_comentario"></div>
            </div> 
            </div>

           <form action="#" method="POST">
              <button name="submit-voltar" type="submit" class="w3-btn w3-xlarge">Voltar</button>
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
  $(document).ready(function(){  
    $('#formulario_comentario').on('submit', function(event){

      var user_logado = "<?php if(isset($_SESSION["UTILIZADOR"])){ echo "sim"; }else{ echo "nao"; }?>";
                  
        if( user_logado == "nao"){
        alert("É preciso estar logado para comentar!");
        location.href='userEntrar.php';
      }else{
        event.preventDefault();
        var dados_formulario = $(this).serialize();
        $.ajax({
          url:"adicionar_comentario.php",
          method:"POST",
          data: dados_formulario,
          dataType:"JSON",
          success:function(dados){
            if(dados.erro != ''){
                $('#formulario_comentario')[0].reset();
                $('#mensagem_comentario').html(dados.erro);
                
            }else{
              alert("Comentario enviado com sucesso");
              $('#formulario_comentario')[0].reset();
              carregar_comentario();
            }
          }
        })
      }
    });


    carregar_comentario();

    function carregar_comentario(){

      $.ajax({
        url: "buscar_comentario.php",
        data: {id_post: <?php echo $id_post;?>}, 
        method:"POST",
        
        success:function(dados){

          $('#mostrar_comentario').html(dados)

        }
      })

    }

    $(document).on("click", ".reply", function(){

      $('.reply-form').html('');
      var id_post = <?php echo $id_post; ?>;
      var id_comentario = $(this).attr("id");
      var user_reply = $(this).attr("user_reply");
      var user_logado = "<?php if(isset($_SESSION["UTILIZADOR"])){ echo "sim"; }else{ echo "nao"; }?>";
                  
      if( user_logado == "nao"){
        alert("É preciso estar logado para comentar!");
        location.href='userEntrar.php';
      }else{

      $('#reply_form_'+id_comentario).
      html('<div class="panel panel-default">\
        <div class="panel-heading">\
          <b>Reposta a '+ user_reply +'</b>\
          <button type="button" class="btn btn-default cancelar-reply" id_comentario="'+id_comentario+'">Cancelar</button>\
        </div>\
        <form method="POST" id="formulario_resposta">\
          <div class="panel-body justificado">\
            <textarea name="conteudo_comentario" id="conteudo_comentario" class="w3-input" placeholder="Digite um Comentário..." rows="4"></textarea>\
            <input type="hidden" name="id_post" id="id_post" value="'+id_post+'">\
            <input type="hidden" name="id_comentario" id="id_comentario" value="'+id_comentario+'">\
          </div>\
          <div class="panel-footer" align="right">\
            <input type="submit" class="btn btn-default" name="submeter" id="submeter" value="Responder">\
          </div>\
        </form>\
      </div>');
      }
    });

    $(document).on('submit', '#formulario_resposta', function(event){

      event.preventDefault();
      var dados_resposta = $(this).serialize();
      var user_logado = "<?php if(isset($_SESSION["UTILIZADOR"])){ echo "sim"; }else{ echo "nao"; }?>";
                  
      if( user_logado == "nao"){
        alert("É preciso estar logado para votar!");
        location.href='userEntrar.php';
      }else{
        $.ajax({
          url:"adicionar_comentario.php",
          method:"POST",
          data: dados_resposta,
          dataType:"JSON",
          success:function(dados){
            if(dados.erro != ''){
                $('#formulario_resposta')[0].reset();
                $('#mensagem_comentario').html(dados.erro);
                
            }else{
                alert("Resposta enviada com sucesso");
                $('.reply-form').html('');
                carregar_comentario();
            }
          }
        })
      }
    });

    $(document).on("click", ".cancelar-reply", function(){
        $('#reply_form_'+ $(this).attr("id_comentario") ).html('');
    });

    $(document).on("click", "#atualizar", function(){
        carregar_comentario();
    });
    

  });
                
                function like(id){
                  var total_votos = parseInt(document.getElementById("total_votos_comentario_"+id).innerText);
                  var voto = $("#like_button_"+id).attr("voto");
                  var user_logado = "<?php if(isset($_SESSION["UTILIZADOR"])){ echo "sim"; }else{ echo "nao"; }?>";
                  
                  if( user_logado == "nao"){
                    alert("É preciso estar logado para votar!");
                    location.href='userEntrar.php';
                  }else{
                    
                    if(voto == "dislike" || voto == "nao"){
                      $.ajax({
                        url:'update_vote_comentario.php',
                        type: 'POST',
                        data:'action=like&id='+id,
                        success: function(result){}
                      })
                      if(voto == "dislike"){total_votos = total_votos + 2;}
                      if(voto == "nao"){total_votos = total_votos + 1;}
                      document.getElementById("total_votos_comentario_"+id).innerHTML = total_votos;
                      voto = "like";
                      document.getElementById("like_button_"+id).setAttribute('voto', voto);
                      document.getElementById("dislike_button_"+id).setAttribute('voto', voto);


                    } else {
                      $.ajax({
                          url:'update_vote_comentario.php',
                          type: 'POST',
                          data:'action=remove&id='+id,
                          success: function(result){}
                        })
                        total_votos = total_votos - 1;
                        document.getElementById("total_votos_comentario_"+id).innerHTML = total_votos;
                        voto = "nao";
                        document.getElementById("like_button_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_button_"+id).setAttribute('voto', voto);
                    }
                  }
                }

                function dislike(id){
                  var total_votos = parseInt(document.getElementById("total_votos_comentario_"+id).innerText);
                  var voto = $("#dislike_button_"+id).attr("voto");
                  var user_logado = "<?php if(isset($_SESSION["UTILIZADOR"])){ echo "sim"; }else{ echo "nao"; }?>";
                  
                  if( user_logado == "nao"){
                    alert("É preciso estar logado para votar!");
                    location.href='userEntrar.php';
                  }else{

                    if(voto == "like" || voto == "nao"){
                      $.ajax({
                        url:'update_vote_comentario.php',
                        type: 'POST',
                        data:'action=dislike&id='+id,
                        success: function(result){}
                      })
                      if(voto == "like"){total_votos = total_votos - 2;}
                      if(voto == "nao"){total_votos = total_votos - 1;}
                      document.getElementById("total_votos_comentario_"+id).innerHTML = total_votos;
                      voto = "dislike";
                      document.getElementById("like_button_"+id).setAttribute('voto', voto);
                      document.getElementById("dislike_button_"+id).setAttribute('voto', voto);
                    } else {
                      $.ajax({
                          url:'update_vote_comentario.php',
                          type: 'POST',
                          data:'action=remove&id='+id,
                          success: function(result){}
                        })
                        total_votos = total_votos + 1;
                        document.getElementById("total_votos_comentario_"+id).innerHTML = total_votos;
                        voto = "nao";
                        document.getElementById("like_button_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_button_"+id).setAttribute('voto', voto);
                    }
                  }
                }

                function like_post(id){
                  var total_votos = parseInt(document.getElementById("total_votos_post_"+id).innerText);
                  var voto = $("#like_post_"+id).attr("voto");
                  var user_logado = "<?php if(isset($_SESSION["UTILIZADOR"])){ echo "sim"; }else{ echo "nao"; }?>";
                  
                  if( user_logado == "nao"){
                    alert("É preciso estar logado para votar!");
                    location.href='userEntrar.php';
                  }else{

                    if(voto == "dislike" || voto == "nao"){
                      $.ajax({
                        url:'update_vote.php',
                        type: 'POST',
                        data:'action=like&id='+id,
                        success: function(result){}
                      })
                      if(voto == "dislike"){total_votos = total_votos + 2;}
                      if(voto == "nao"){total_votos = total_votos + 1;}
                      document.getElementById("total_votos_post_"+id).innerHTML = total_votos;
                      voto = "like";
                      document.getElementById("like_post_"+id).setAttribute('voto', voto);
                      document.getElementById("dislike_post_"+id).setAttribute('voto', voto);
                      document.getElementById("like_post_"+id).setAttribute('class', 'btn w3-purple w3-hover-purple btn-sm');
                      document.getElementById("dislike_post_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                    } else {
                      $.ajax({
                          url:'update_vote.php',
                          type: 'POST',
                          data:'action=remove&id='+id,
                          success: function(result){}
                        })
                        total_votos = total_votos - 1;
                        document.getElementById("total_votos_post_"+id).innerHTML = total_votos;
                        voto = "nao";
                        document.getElementById("like_post_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_post_"+id).setAttribute('voto', voto);
                        document.getElementById("like_post_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                        document.getElementById("dislike_post_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                    }
                  }
                }

                function dislike_post(id){
                  var total_votos = parseInt(document.getElementById("total_votos_post_"+id).innerText);
                  var voto = $("#dislike_post_"+id).attr("voto");
                  var user_logado = "<?php if(isset($_SESSION["UTILIZADOR"])){ echo "sim"; }else{ echo "nao"; }?>";
                  
                  if( user_logado == "nao"){
                    alert("É preciso estar logado para votar!");
                    location.href='userEntrar.php';
                  }else{

                    if(voto == "like" || voto == "nao"){
                      $.ajax({
                        url:'update_vote.php',
                        type: 'POST',
                        data:'action=dislike&id='+id,
                        success: function(result){}
                      })
                      if(voto == "like"){total_votos = total_votos - 2;}
                      if(voto == "nao"){total_votos = total_votos - 1;}
                      document.getElementById("total_votos_post_"+id).innerHTML = total_votos;
                      voto = "dislike";
                      document.getElementById("like_post_"+id).setAttribute('voto', voto);
                      document.getElementById("dislike_post_"+id).setAttribute('voto', voto);
                      document.getElementById("dislike_post_"+id).setAttribute('class', 'btn w3-purple w3-hover-purple btn-sm');
                      document.getElementById("like_post_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                    } else {
                      $.ajax({
                          url:'update_vote.php',
                          type: 'POST',
                          data:'action=remove&id='+id,
                          success: function(result){}
                        })
                        total_votos = total_votos + 1;
                        document.getElementById("total_votos_post_"+id).innerHTML = total_votos;
                        voto = "nao";
                        document.getElementById("like_post_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_post_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_post_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                        document.getElementById("like_post_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                    }
                  }
                }

              </script>