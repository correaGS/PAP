<?php 

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

if ( isset($_POST['botao-voltar']) ) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
}
if ( isset($_POST['botao-cancelar-minha-conta']) ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: userCancelarConta.php");
}

$msgTemporaria = "";
$mensagemErroNome = "";
$mensagemErroSenha = "";
$mensagemErroFoto = "";



if ( !isset($_SESSION["UTILIZADOR"])) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
} else {
    // ler informações de conta 
    
    $codigo = $_SESSION["UTILIZADOR"];
    $sql = userVerification();
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('ss', $codigo, $codigo);
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
         
            $pic = $rowUsers['foto'];
            
            if ( !isset($_POST["nome"])) {
                
                $nome = $rowUsers['nome'];
                $visivel = $rowUsers['visibilidade'];
                $receberMsgs = $rowUsers['marketing'];
                $bio = $rowUsers['bio'];
                $bio = str_replace('\r',"\r",str_replace('\n',"\n",$bio));
                $nacionalidade = $rowUsers['nacionalidade'];
                $genero = $rowUsers['genero'];

                                
            }

             else {
                
                $podeRegistar = "Sim"; 
                
                ///////// em modo de alteração - filtrar e validar campos
                
                $nome = mysqli_real_escape_string($_conn, $_POST['nome']);
                $nome = trim($nome);

                $bio = mysqli_real_escape_string($_conn, $_POST['bio']);
                $bio = trim($bio);
             
                $receberMensagens=$_POST['receberMensagens'];
                if ( $receberMensagens == "Sim") { 
                    $receberMsgs = 2;
                } else {
                    $receberMsgs = 1;
                }

                $visivel=$_POST['visibilidade'];
                if ( $visivel == "Sim") { 
                    $visivel = 2;
                } else {
                    $visivel = 1;
                }

                if (strlen(trim($nome))<2) {
                    $mensagemErroNome="O nome é demasiado curto!";
                    $podeRegistar = "Nao"; 
                }
            }

        }
    } else {
        echo "STATUS ADMIN (Editar conta): " . mysqli_error($_conn);
    }           
                    
     mysqli_stmt_close($stmt);
    
}

if ( isset($_POST['botao-gravar-alteracoes']) ) {

        
        
        if ( $podeRegistar == "Sim" )  {
            
           
                ///////////////////////////////////
                // ALTERA
                //////////////////////////////////
                
            
                $nome = strip_tags($nome);
                $bio = strip_tags($bio);
                $genero = $_POST["genero"];
                $nacionalidade = $_POST["nacionalidade"];

            
                $sql= alterarPerfil();
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                
                    mysqli_stmt_bind_param($stmt, "sisiis", $nome, $visivel, $bio, $genero, $nacionalidade,$codigo);
                    mysqli_stmt_execute($stmt);
                    
                    $sql= alterarMarketing();
                
                if ( $stmt2 = mysqli_prepare($_conn, $sql) ) {
                    mysqli_stmt_bind_param($stmt2, "is",$receberMsgs, $codigo);
                    mysqli_stmt_execute($stmt2);

                    $msgTemporaria = "Definições de conta alteradas com sucesso.";
                    
                    // atualizar variável de sessão, a questão de receber mensagens de marketing não
                    // é uma variável de sessão, não é necessário guardar em sessão.
                    
                    $_SESSION["NOME_UTILIZADOR"] = $nome;
                    
                } else{
                    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($_conn);
                    echo "STATUS ADMIN (alterar definições): " . mysqli_error($_conn);
                }
                mysqli_stmt_close($stmt2);
                    
                } else{
                    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($_conn);
                    echo "STATUS ADMIN (alterar definições): " . mysqli_error($_conn);
                }
                mysqli_stmt_close($stmt);
                
        }
            
            
}

if ( isset($_POST["apagar-post"])  ) {
    
    $sql= apagarPost();
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        $idPost = $_POST["idPost"];
        mysqli_stmt_bind_param($stmt, "i", $idPost);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        
        
    } else {

        $stmt->close();
        // falhou a atualização
        
        echo "STATUS ADMIN (apagar post): " . mysqli_error($_conn);
    }
    
}

if ( isset($_POST["apagar-comentario"])  ) {
    
  $sql= apagarComentario();
  
  if ( $stmt = mysqli_prepare($_conn, $sql) ) {
      $idComentario = $_POST["idComentario"];
      mysqli_stmt_bind_param($stmt, "i", $idComentario);
      mysqli_stmt_execute($stmt);

      mysqli_stmt_close($stmt);
      
      
  } else {

      $stmt->close();
      // falhou a atualização
      
      echo "STATUS ADMIN (apagar comentario): " . mysqli_error($_conn);
  }
  
}
 
?>


<!DOCTYPE html>
<html>
<head>
<title>Editar conta</title>
<?php include_once "style.html";?>
<link href="cropperjs/cropper.min.css" rel="stylesheet" type="text/css"/>


<style>

		.image_area {
		  position: relative;
		}

		.profile_image {
		  	display: block;
		  	max-width: 100%;
		}

		.preview {
  			overflow: hidden;
  			width: 160px; 
  			height: 160px;
  			margin: 10px;
  			border: 1px solid red;
		}

		.modal-lg{
  			max-width: 1000px !important;
		}

		.overlay {
		  position: absolute;
		  bottom: 10px;
		  left: 0;
		  right: 0;
		  background-color: rgba(246, 243, 251, 0.5);
		  overflow: hidden;
		  height: 0;
		  transition: .5s ease;
		  width: 100%;
		}

		.image_area:hover .overlay {
		  height: 50%;
		  cursor: pointer;
		}

		.text {
		  color: #333;
		  font-size: 20px;
		  position: absolute;
		  top: 50%;
		  left: 50%;
		  -webkit-transform: translate(-50%, -50%);
		  -ms-transform: translate(-50%, -50%);
		  transform: translate(-50%, -50%);
		  text-align: center;
		}
		
</style>

</head>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center">
<div class="center">
      <h1>Alterar Perfil</h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
      
      <h2>Dados</h2>
      <p>Se pretende alterar a sua senha use a opção <a href="userRecuperarSenha.php">recuperar senha</a></p>
      
        <div class="w3-section">
            <div class="image_area">
                <label for="upload_image">
                    <img src="<?php echo $pic;?>" id="uploaded_image" class="img-responsive profile_image img-circle profile-img"/>
                    <div class="overlay">
                        <div class="text">Trocar Imagem de Perfil</div>
                    </div>
                    <input type="file" name="image" class="image"  accept="image/*" id="upload_image" style="display: none;">
                </label>
            </div>
        </div>
        <div class="w3-section">
        <form form action="#" method="POST" enctype="multipart/form-data">
        <p><?php echo $msgTemporaria;?></p>
        <div class="w3-section">
          <label>Nome</label><p><?php echo $mensagemErroNome;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;"type="text" id="nome" name="nome" value="<?php echo $nome;?>" required placeholder="Nome">
        </div>
        <div class="w3-section">
        <label>Visibilidade</label>
        <select class="w3-select w3-border w3-round-large" name="visibilidade">
               <option value="Sim" <?php if ($visivel == 2 ) { echo " selected"; } ?>>Privado</option>
               <option value="Não" <?php if ($visivel == 1 ) { echo " selected"; } ?>>Publico</option>
       </select>
        </div>
        <div class="w3-section">
        <label>Genero</label>
        <select class="w3-select w3-border w3-round-large w3-margin-bottom" name="genero" id="genero">
          <option value=""  <?php if(empty($genero)){echo"selected";}?>>Selecione um genero...</option>
            <?php
              $genero_sql = get_genero();
              $resultado_genero = mysqli_query($_conn, $genero_sql);
              while ($row_genero = mysqli_fetch_array($resultado_genero)){
                ?><option value="<?php echo $row_genero["id_genero"]?>" <?php if($genero == $row_genero["id_genero"]){echo"selected";}?>><?php echo $row_genero["descricao"]?></option><?php
              }mysqli_free_result($resultado_genero);
            ?>
        </select>
        </div>
        <div class="w3-section">
        <label>Nacionalidade</label>
        <select class="w3-select w3-border w3-round-large w3-margin-bottom" name="nacionalidade" id="nacionalidade">
          <option value=""  <?php if(empty($nacionalidade)){echo"selected";}?>>Selecione uma nacionalidade...</option>
            <?php
              $nacionalidade_sql = get_nacionalidade();
              $resultado_nacionalidade = mysqli_query($_conn, $nacionalidade_sql);
              while ($row_nacionalidade = mysqli_fetch_array($resultado_nacionalidade)){
                ?><option value="<?php echo $row_nacionalidade["id_nacionalidade"]?>" <?php if($nacionalidade == $row_nacionalidade["id_nacionalidade"]){echo"selected";}?>><?php echo $row_nacionalidade["descricao"]?></option><?php
              }mysqli_free_result($resultado_nacionalidade);
            ?>
        </select>
        </div>
        <div class="w3-section">
        <label>Bios</label>
        <textarea class="w3-input w3-margin-bottom w3-border w3-round-large w3-theme-l5" style="width:100%; overflow-x: hidden;overflow-y: scroll;" rows="4" type="text" maxlength="500" name="bio" placeholder="Digite algo..."><?php echo $bio;?></textarea>
        </div>
        <div class="w3-section">
        <label>Pretendo receber mensagens de marketing</label>
         <select class="w3-select w3-border w3-round-large" name="receberMensagens">
                <option value="Sim" <?php if ($receberMsgs == 2 ) { echo " selected"; } ?>>Sim</option>
                <option value="Não" <?php if ($receberMsgs == 1 ) { echo " selected"; } ?>>Não</option>
        </select>
        </div>
        
        <input class="w3-btn" type="submit" value="Gravar Alterações" id="botao-gravar-alteracoes" name="botao-gravar-alteracoes">
        <button form="cancelar" name="botao-cancelar-minha-conta" type="submit" class="w3-btn">Cancelar Conta</button>
      </form>
      <form id="cancelar" action="#" method="POST">
      </form>
</div>
<div id="publicacoes" class="w3-section">
      <?php
    $username = "";
    $post = "";
    $dataHora = "";
    $sql = verPostsUtilizador($codigo);
    $resultadoTabela = mysqli_query($_conn, $sql);           
     if (mysqli_num_rows($resultadoTabela) > 0) {
          
          ?>
          <hr><div class='w3-section'><h2>Posts</h2>
          <button name="ver-comentarios" type="button" class="w3-btn"><a href="userEditarConta.php#comentarios">Ver Comentários</a></button>
          <?php
          while($rowTabela = mysqli_fetch_assoc($resultadoTabela)) {
            $id_post = $rowTabela["id"];
            $post_categoria = $rowTabela["post_categoria"];
            $main_categoria = $rowTabela["main_categoria"];
            $titulo = $rowTabela["titulo"];
            $titulo = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $titulo);
            $titulo = wordwrap($titulo, 100, "<br>\n",TRUE);
            $visibilidade = $rowTabela["visibilidade"];
            $username = $rowTabela["username"];
            $post = $rowTabela["post"];
            $post = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $post);
            $post = wordwrap($post, 100, "<br>\n",TRUE);
            $dataHora = $rowTabela["data_hora"];
           ?>
           <div class="w3-padding-16 w3-center">
           <div class="w3-section w3-panel w3-round-large w3-theme-l2 w3-card-4 w3-padding" style="max-width: 750px; margin: auto;">

           <div class="w3-col w3-center" style="display: flex; justify-content:center; align-items:center; width: 50px;">

              <?php
                $votos = countVotes($id_post);
                $resultadoVotos = mysqli_query($_conn, $votos);
                if (mysqli_num_rows($resultadoVotos) > 0){
                  while($rowTabelaVotos = mysqli_fetch_array($resultadoVotos)) {
                  $total_votos = $rowTabelaVotos["likes"];
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
                        $voto="like";
                      } else if($row_ja_votou['vote']==-1){
                        $voto="dislike";
                      }
                  }
                } else {
                   $voto="nao";
                }
                
                $stmt->free_result();
                $stmt->close();
              ?>
             
              <div class="w3-padding">
                <div><button type="button" class="btn <?php if($voto=="like"){echo "w3-purple";}else{echo"w3-theme";}?> w3-hover-purple btn-sm" voto="<?php echo $voto;?>" id="like_button_<?php echo $id_post;?>" onclick="like('<?php echo $id_post;?>')"><i class="fa-solid fa-caret-up"></i></button></div>
                <div><b id="total_votos_post_<?php echo $id_post;?>"><?php echo $total_votos;?></b></div>
                <div><button type="button" class=" btn <?php if($voto=="dislike"){echo "w3-purple";}else{echo"w3-theme";}?> w3-hover-purple btn-sm" voto="<?php echo $voto;?>" id="dislike_button_<?php echo $id_post;?>" onclick="dislike('<?php echo $id_post;?>')"><i class="fa-solid fa-caret-down"></i></button></div>
                <div class="w3-padding"><button  type="button" class="btn w3-theme w3-hover-purple btn-sm" onClick="location.href='post.php?id=<?php echo $id_post; ?>'"><i class="fa-solid fa-message"></i></button></div>
                <form action="#" id="delete_form" onSubmit="return confirm('Clique em OK para deletar a publicação')" method="POST">
                <button type="submit" name="apagar-post" class="btn w3-theme w3-hover-purple btn-lg"><i class="fa-solid fa-trash-can"></i></button>
                <input id="idPost" name="idPost" type="hidden" value="<?php echo $rowTabela["id"]; ?>">
                </form>
            </div>
           </div>

           <div class="w3-rest w3-padding">
           <div class="w3-section w3-center">
           <div class="w3-cell"style="vertical-align: middle;"><img src=<?php echo $pic;?> class="w3-image img-circle" style=" width:45px; height: 45px; min-width:45px; nin-height: 45px; max-width:45px; max-height: 45px;"></div>
           <div class="w3-cell"style="vertical-align: middle;"><b style="padding-left: 10px;">
           <?php if(empty($main_categoria)){
            echo "Por ".$username." • Em ". $post_categoria ." • ". $dataHora;}
            else{echo "Por ".$username." • Em ". $main_categoria." - ". $post_categoria ." • ". $dataHora;}
            ?>
          </b></div>
           </div>
           <div class="w3-section w3-center" >
           <p><h2 class="justificado text-width"><?php echo $titulo;?></h2></p>
           <p class="justificado text-width"><?php echo $post ?></p>
                <div class="w3-padding"><button type="button" class="btn w3-theme w3-hover-purple btn-sm" onClick="location.href='post.php?id=<?php echo $id_post; ?>'"><b>Ver Mais</b></button></div>
           </div>
           </div>
           </div>
           </div>
          
           <?php
       }
       ?></div><?php
      }mysqli_free_result($resultadoTabela);
       ?>
</div>
       <div id="comentarios" class="w3-section">
       <?php
        $sql_comentarios = verComentariosUtilizador($codigo);
        $resultadoComentarios = mysqli_query($_conn, $sql_comentarios);           
        if (mysqli_num_rows($resultadoComentarios) > 0) {
        ?>
          <hr><div class='w3-section w3-padding-large'><h2>Comentários</h2>
          <button name="ver-publicacoes" type="button" class="w3-btn"><a href="userEditarConta.php#publicacoes">Ver Publicações</a></button>
        
          <?php
          while($rowComentario = mysqli_fetch_assoc($resultadoComentarios)) {
            $id_comentario = $rowComentario["id"];
            $visibilidade = $rowComentario["visibilidade"];
            $username = $rowComentario["user"];
            $text = $rowComentario["text"];
            $text = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $text);
            $text = wordwrap($text, 100);
            $dataHora = $rowComentario["data_hora"];
            $id_post_comentado = $rowComentario["id_post"];
            $id_reply = $rowComentario["id_reply"];
            $user_reply = $rowComentario["user_reply"];
            $foto = $pic;
            ?>
            <div class="w3-padding-16 w3-center">
            <div class="w3-section w3-panel w3-round-large w3-theme-l2 w3-card-4 w3-padding" style="max-width: 750px; margin: auto;">
 
            <div class="w3-col w3-center" style="display: flex; justify-content:center; align-items:center; width: 50px;">

              
               <div class="w3-padding">
                 <form action="#" id="delete_form_comentario" onSubmit="return confirm('Clique em OK para deletar o comentario')" method="POST">
                 <button type="submit" name="apagar-comentario" class="btn w3-theme w3-hover-purple btn-lg"><i class="fa-solid fa-trash-can"></i></button>
                 <input id="idComentario" name="idComentario" type="hidden" value="<?php echo $rowComentario["id"]; ?>">
                 </form>
             </div>
            </div>
 
            <div class="w3-rest w3-padding">
            <div class="w3-section w3-center">
            <div class="w3-cell"style="vertical-align: middle;"><img src=<?php echo $pic;?> class="w3-image img-circle" style=" width:45px; height: 45px; min-width:45px; nin-height: 45px; max-width:45px; max-height: 45px;"></div>
            <div class="w3-cell"style="vertical-align: middle;"><b style="padding-left: 10px;">
            <?php
              if(empty($id_reply)){
                ?>
                <b><?php echo $username;?> • </b><i><?php echo $dataHora;?></i>
                <?php

              } else {
                if(empty($id_reply)){
                    ?>
                    <b><?php echo $username;?> em resposta a [DELETADO]• </b><i><?php echo $dataHora;?></i>
                    <?php
                  } else {
                    ?>
                    <b><?php echo $username;?> em resposta a <?php echo $user_reply;?> • </b><i><?php echo $dataHora;?></i>
                    <?php
                  }
              }
             ?>
           </b></div>
            </div>
            <div class="w3-section w3-center" >
            <p class="justificado"><?php echo $text ?></p>
                 <div class="w3-padding"><button type="button" class="btn w3-theme w3-hover-purple btn-sm" onClick="location.href='post.php?id=<?php echo $id_post_comentado; ?>'"><b>Ver Mais</b></button></div>
            </div>
            </div>
            </div>
            </div>
           
            <?php
          }
          ?></div><?php
        }mysqli_free_result($resultadoComentarios);
            
            ?>
       </div>
       
       
       <form id="voltar" action="#" method="POST">
       <button form="voltar" name="botao-voltar" type="submit" class="w3-btn w3-xlarge">Voltar</button>
      </form>
    </div>
</div>
</div>
<?php include_once "footer.php";?>
</div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5> Cortar Imagem </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <span aria-hidden="true">X</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">
                            <img src="" id="sample_image" class="profile_image">
                        </div>
                        <div class="col-md-4">
                            <div class="preview"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="crop" class="btn btn-primary">Cortar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<script src="cropperjs/cropper.min.js" type="text/javascript"></script>

<script>
//SCRIPT para cortar e alterar imagem de perfil

    $(document).ready(function(){

        var user = "<?php echo $codigo;?>";

        var confirm = false;

        var $modal = $('#modal');

        var image = document.getElementById('sample_image');

        var cropper;

        $('#upload_image').change(function(event){

            var files = event.target.files;

            var done = function(url){
                image.src = url;
                $modal.modal('show');
            };

            if(files && files.length > 0){
                reader = new FileReader();
                reader.onload = function(event){
                    done(reader.result);
                };
                reader.readAsDataURL(files[0]);
            }

        });

        $modal.on('shown.bs.modal', function(){
            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 2,
                preview:'.preview'
            });
        }).on('hidden.bs.modal', function(){
            cropper.destroy();
            cropper = null;
        });

        $('#crop').click(function(){
            canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400
            });

            canvas.toBlob(function(blob){
                url = URL.createObjectURL(blob);
                var reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function(){
                    var base64data = reader.result;
                    $.ajax({
                        url:'upload_image.php',
                        method: 'POST',
                        data: {image: base64data, user: user},
                        success: function(data){
                            $modal.modal('hide');
                            $('#uploaded_image').attr('src', data)
                            alert("Imagem de Perfil Alterada!");
                        }
                    });
                };
            });
        });

    });
</script>

<script>


                      
                function like(id){
                  var total_votos = parseInt(document.getElementById("total_votos_post_"+id).innerText);
                  var voto = $("#like_button_"+id).attr("voto");
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
                        document.getElementById("like_button_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_button_"+id).setAttribute('voto', voto);
                        document.getElementById("like_button_"+id).setAttribute('class', 'btn w3-purple w3-hover-purple btn-sm');
                        document.getElementById("dislike_button_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
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
                          document.getElementById("like_button_"+id).setAttribute('voto', voto);
                          document.getElementById("dislike_button_"+id).setAttribute('voto', voto);
                          document.getElementById("like_button_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                          document.getElementById("dislike_button_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                    }
                  }
                }

                function dislike(id){
                  var total_votos = parseInt(document.getElementById("total_votos_post_"+id).innerText);
                  var voto = $("#dislike_button_"+id).attr("voto");
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
                      document.getElementById("like_button_"+id).setAttribute('voto', voto);
                      document.getElementById("dislike_button_"+id).setAttribute('voto', voto);
                      document.getElementById("dislike_button_"+id).setAttribute('class', 'btn w3-purple w3-hover-purple btn-sm');
                      document.getElementById("like_button_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
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
                        document.getElementById("like_button_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_button_"+id).setAttribute('voto', voto);
                        document.getElementById("dislike_button_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                        document.getElementById("like_button_"+id).setAttribute('class', 'btn w3-theme w3-hover-purple btn-sm');
                    }
                  }
                }

              </script>