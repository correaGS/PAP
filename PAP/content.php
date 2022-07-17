<!-- Header -->
<header class="w3-container w3-theme w3-center" style="padding:128px 16px">
  <h1 class="w3-margin w3-jumbo">Fórum Escolar</h1>
  <div class="w3-row-padding w3-padding-32 w3-row w3-center" style="width: 65%; margin:auto;">
  <form action="pesquisa.php" method="POST">
    <div class="w3-threequarter w3-padding-16">
      <input class="w3-input w3-border w3-round w3-white" name="pesquisa_geral" id="pesquisa_geral" type="text" required>
      <input name="tipo_pesquisa" id="tipo_pesquisa" type="hidden" value="simples">
    </div>
    <div class="w3-quarter w3-padding-16">
      <input class="w3-btn w3-border w3-round" name="pesquisar" id="pesquisar" type="submit" value="Pesquisar">
    </div>
  </form> 
  </div>
  <div class="w3-row-padding w3-row w3-container">
    
  <button onclick="document.getElementById('popup').style.display='block'" class="w3-btn w3-border w3-round">Pesquisa Avançada</button>

<!-- Popup -->
<div id="popup" class="w3-modal w3-animate-opacity">
  <div class="w3-modal-content w3-card-4 w3-theme w3-animate-opacity">
    <div class="w3-container w3-section">
      <span onclick="document.getElementById('popup').style.display='none'"
      class="w3-button w3-display-topright">&times;</span>
      <div class="w3-content w3-center w3-padding-large">
        <h4>Pesquisa Avançada</h4>
    <div class="w3-section">
    <form id="advanced_search" action="pesquisa.php" method="POST">
    <label><h4>Categoria</h4></label>
      <select class="w3-select w3-border w3-round-large w3-text-black w3-margin-bottom" name="categoria" id="categoria">
          <option value=""  selected>Selecione uma categoria...</option>
            <?php
              $categoria_sql = get_categoria();
              $resultado_categorias = mysqli_query($_conn, $categoria_sql);
              while ($row_categoria = mysqli_fetch_array($resultado_categorias)){
                ?><option value="<?php echo $row_categoria["id"]?>"><?php echo $row_categoria["descricao"]?></option><?php
              }mysqli_free_result($resultado_categorias);
            ?>
        </select>
      <p id="select_subcategoria"></p>
      <script>
        var modal = document.getElementById('popup');

        window.onclick = function(event) {
          if (event.target == modal) {
            modal.style.display = "none";
          }
        }
          $(document).ready(function() {

          $('#categoria').on('change', function() {

            var id_categoria = this.value;
            $.ajax({
              url: "buscar_subcategoria.php",
              type: "POST",
              data: { id_categoria: id_categoria },
              cache: false,
              success: function(result){
                $("#select_subcategoria").html(result)
              }
            });

          });

          });
      </script>
      <label><h4>Titulo</h4></label> <input class="w3-input w3-border w3-round w3-text-black w3-margin-bottom" name="pesquisa_titulo" id="pesquisa_titulo" type="text">
      <label><h4>Publicação</h4></label><input class="w3-input w3-border w3-round w3-text-black w3-margin-bottom" name="pesquisa_post" id="pesquisa_post" type="text">
      <input name="tipo_pesquisa" id="tipo_pesquisa" type="hidden" value="advanced">
      <input class="w3-btn w3-border w3-round w3-padding" name="submeter" id="submeter" type="submit" value="Pesquisar">
  </form>
  </div>
      </div>
    </div>
  </div>
</div>


  </div>
</header>

<div class="content w3-row-padding w3-padding-64 w3-container w3-theme-l5">
<div class="center">

<div id ="posts" class="w3-content">
  <h1  class="w3-center">Publicações</h1>
  <br>
<?php

if (isset($_GET['pagenum'])) {
  $pagenum = $_GET['pagenum'];
} else {
  $pagenum = 1;
}
$pagePosts = 4;
$offset = ($pagenum - 1) * $pagePosts;


$username = "";
$post = "";
$dataHora = "";
$sql = countPosts();
   $allPosts = mysqli_query($_conn, $sql);           
   if (mysqli_num_rows($allPosts) > 0) {
      $total_rows = mysqli_fetch_array($allPosts)[0];
      $total_pages = $total_rows / $pagePosts;
      $total_pages = ceil($total_pages);
      mysqli_free_result($allPosts);

      $sql = verPosts($offset, $pagePosts);
      $resultadoTabela = mysqli_query($_conn, $sql);   
      if (mysqli_num_rows($resultadoTabela) > 0) {
       $ctd = 0;
       while($rowTabela = mysqli_fetch_array($resultadoTabela)) {
           $ctd=$ctd+1;
           $id_post = $rowTabela["id"];
           $post_categoria = $rowTabela["post_categoria"];
           $main_categoria = $rowTabela["main_categoria"];
           $titulo = $rowTabela["titulo"];
           $titulo = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $titulo);
           $titulo = wordwrap($titulo, 100, "<br>\n",TRUE);
           $visibilidade = $rowTabela["visibilidade"];
           $pic = $rowTabela["foto"];
           $username = $rowTabela["username"];
           $post = $rowTabela["post"];
           $post = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $post);
           $post = wordwrap($post, 100, "<br>\n",TRUE);
           $dataHora = $rowTabela["data_hora"];
           $postLenght = strip_tags($post);
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
                
                <div class="w3-padding">
                  <form action="reportar.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id_post; ?>">
                    <button  type="submit" name="report" value="post" class="btn w3-theme w3-hover-purple btn-sm"><i class="fa-solid fa-flag"></i></button>
                  </form>
                </div>
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
           <p class="justificado text-width">
             <?php
              if (strlen($postLenght) > 500){
                echo substr_close_tags($post);
                ?>
                <div class="w3-padding"><button type="button" class="btn w3-theme w3-hover-purple btn-sm" onClick="location.href='post.php?id=<?php echo $id_post; ?>'"><b>Ver Mais</b></button></div>
             <?php 
              }else{echo substr_close_tags($post);}
             ?>
            </p>
           </div>
           </div>
           </div>
           </div>
           <?php
       }
      }mysqli_free_result($resultadoTabela);
     
    }
       ?>
       <div class="w3-center">
          <ul class="pagination">
        <li><a href="?pagenum=1#posts" class="w3-theme">First</a></li>
        <li class="<?php if($pagenum <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($pagenum <= 1){ echo '#posts'; } else { echo '?pagenum='.($pagenum - 1)."#posts"; } ?>" class="<?php if($pagenum <= 1){ echo 'w3-theme-d5'; } else { echo 'w3-theme'; } ?>">Prev</a>
        </li>
        <li class="<?php if($pagenum >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($pagenum >= $total_pages){ echo '#posts'; } else { echo "?pagenum=".($pagenum + 1)."#posts"; } ?>" class="<?php if($pagenum >= $total_pages){ echo 'w3-theme-d5'; } else { echo 'w3-theme'; } ?>">Next</a>
        </li>
        <li><a href="?pagenum=<?php echo $total_pages; ?>#posts" class="w3-theme">Last</a></li>
    </ul>
  </div>
    <div class="w3-row-padding w3-padding-32 w3-container">
  <div class="w3-content w3-center"> 
    <a href="perfis.php" class=" w3-button w3-theme w3-padding-large w3-text-grey" style="margin: 2px;">Ver Perfis Públicos</a>
    <a href="criarPost.php" class=" w3-button w3-theme w3-padding-large w3-text-grey" style="margin: 2px;">Criar Posts</a>
  </div>
  </div>
</div>
</div>
</div>
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