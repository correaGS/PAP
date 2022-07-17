<?php
	session_start();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    error_reporting(E_ALL);
    include_once  'conexaobasedados.php'; 
    include_once 'comandosbasedados.php';

    $tipo_pesquisa = ""; 
    $pesquisa_geral = ""; 
    $pesquisa_titulo = ""; 
    $pesquisa_post = ""; 
    $id_categoria_pesquisada = "";


    if(isset($_POST["tipo_pesquisa"])){

        $tipo_pesquisa = $_POST["tipo_pesquisa"];

        if($tipo_pesquisa == "simples"){
            
            if(isset($_POST["pesquisa_geral"])){
            $pesquisa_geral = $_POST["pesquisa_geral"];
            }
            
            $pesquisa_sql = pesquisa_posts_simples($pesquisa_geral);

        } else if($tipo_pesquisa == "advanced"){

            if(isset($_POST["pesquisa_titulo"])){
                $pesquisa_titulo = $_POST["pesquisa_titulo"];
            }

            if(isset($_POST["pesquisa_post"])){
                $pesquisa_post = $_POST["pesquisa_post"];
            }

            if(!isset ($_POST["sub_categoria"])){

                if(isset ($_POST["id_categoria_pesquisada"])){

                    $id_categoria_pesquisada = $_POST["id_categoria_pesquisada"];
                }
                 
            }
            
            $pesquisa_sql = pesquisa_posts_advanced($pesquisa_post, $pesquisa_titulo, $id_categoria_pesquisada);

        }

        $resultadoPesquisa = mysqli_query($_conn, $pesquisa_sql);
        
        if (mysqli_num_rows($resultadoPesquisa) > 0) {
            
            echo "<span><p class='text-success'> Foram encontrados ".mysqli_num_rows($resultadoPesquisa)." resultados para a pesquisa</p></span>";

            while($rowTabela = mysqli_fetch_array($resultadoPesquisa)) {

                $id_post = $rowTabela["id"];
                $titulo = $rowTabela["titulo"];
                $titulo = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $titulo);
                $titulo = wordwrap($titulo, 100, "<br>\n",TRUE);
                $post_categoria = $rowTabela["post_categoria"];
                $main_categoria = $rowTabela["main_categoria"];
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
            </div>
           </div>

           <div class="w3-rest w3-padding">
           <div class="w3-section w3-center">
           <div class="w3-cell"style="vertical-align: middle;"><img src=<?php echo $pic;?> class="w3-image w3-circle" style=" width:45px; height: 45px; min-width:45px; nin-height: 45px; max-width:45px; max-height: 45px;"></div>
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
        } else {
            ?>
            <span><p class='text-danger'> Não foram encontrados resultados para a pesquisa</p></span>
            <?php
        }
    }


?>