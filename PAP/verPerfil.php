<?php 

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

$mensagem ="";
$nome = "";        
$apelido = "";
$telemovel = "";
$perfilDisponivel = "SIM";

if ( isset($_POST['submit-voltar']) ) {
    
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: perfis.php");
}

if ( !isset($_SESSION["UTILIZADOR"])) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if (isset($_GET['id']) AND $_GET['id'] != $_SESSION['UTILIZADOR']) {
    
    
    $codigo = $_GET['id']; // código do utilizador...
   
    $sql = verPerfil();
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('s', $codigo);  
    $stmt->execute();

    $perfilUsers = $stmt->get_result();
    
    if ($perfilUsers->num_rows > 0) {
        while ($rowUsers = $perfilUsers->fetch_assoc()) {
            
            $nome = $rowUsers['nome'];
            $pic = $rowUsers['foto'];

                $nome = $rowUsers['nome'];
                $visivel = $rowUsers['visibilidade'];
                $bio = $rowUsers['bio'];
                $bio = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $bio);
                $bio = wordwrap($bio, 100 , "<br>\n",TRUE);
                $nacionalidade = $rowUsers['nacionalidade'];
                $genero = $rowUsers['genero'];
                     mysqli_stmt_close($stmt);
                 }
                 if($visivel != 1) {
                    $perfilDisponivel = "NAO";
                   $mensagem = "Perfil Indisponível";
                } 
          }else {
            $perfilDisponivel = "NAO";
           $mensagem = "Perfil Indisponível";
        } 
    } else {
        $perfilDisponivel = "NAO";
        $mensagem = "Perfil Indisponível";
        // caso alguém use o endereço sem parametros volta 
        // de imediato para a página principal sem dar qualquer
        // tipo de mensagem
        
        // encaminhar para página principal
  		// header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
  		// header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
  		// header("Location: index.php"); // encaminhar de imediato

    }  
?>

<!DOCTYPE html>
<html>
<title>Perfil de <?php echo $_GET['id'];?></title>
<?php include_once "style.html";?>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
    <?php include_once "navbar.php";?>

<div class="content w3-theme w3-padding-64 w3-padding-large w3-center">
    <div class="center">
          <h1>Perfil de <?php echo $_GET['id'];?></h1>
          <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
        <div class="w3-section w3-center">
            <h2>Dados</h2>
            <p><?php echo $mensagem;?></p> 
            <?php if ($perfilDisponivel == "SIM"){?>
            <div class="w3-card-4 w3-center w3-panel w3-round-large w3-padding-large">
                <div class="w3-container w3-center w3-padding">
                  <form action='reportar.php' method='POST' style="float: right;">
                    <input type='hidden' name='id' value='<?php echo $_GET['id'];?>'>
                      <button  type='submit' name='report' value='user' class='btn w3-theme w3-hover-purple btn-sm'><i class='fa-solid fa-flag'></i></button>
                    </form>
                    <div class="w3-section">
                    <img src=<?php echo $pic;?> class="w3-image img-circle w3-center profile-img">
                    </div>
                    <div class="w3-section">
                    <p><label >Nome - <?php echo $nome;?></label></p>
                    <p><label >Nacionalidade - <?php echo $nacionalidade;?></label></p>
                    <p><label >Genero - <?php echo $genero;?></label></p>
                    <p><label >Sobre<?php echo "<p class='text-width'>".$bio."</p>";?></label></p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
      $username = "";
      $post = "";
      $dataHora = "";
      $sql = verPostsUtilizador($codigo);
      $resultadoTabela = mysqli_query($_conn, $sql);           
      if (mysqli_num_rows($resultadoTabela) > 0) {
      ?><div class='w3-section'><h2>Posts</h2><?php
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

       <div class="w3-section w3-center">
       <p><h2 class="justificado"><?php echo $titulo;?></h2></p>
       <p class="justificado text-width"><?php echo $post; ?></p>
       
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
    <?php } ?>
    
      <form action="#" method="POST">
          <button name="submit-voltar" type="submit" class="w3-btn w3-xlarge">Voltar</button>
          </form>
        </div>
        </div>
    </div>
    <?php include_once "footer.php";?> 
</div>
</body>
</html>

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