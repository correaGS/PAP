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
                $pesquisa_geral = trim(mysqli_real_escape_string($_conn,$_POST["pesquisa_geral"]));
                $pesquisa_geral = trim($pesquisa_geral);
                $pesquisa_geral = strip_tags($pesquisa_geral);
            }

        } else if($tipo_pesquisa == "advanced"){

            if(isset($_POST["pesquisa_titulo"])){
                $pesquisa_titulo = trim(mysqli_real_escape_string($_conn,$_POST["pesquisa_titulo"]));
                $pesquisa_titulo = trim($pesquisa_titulo);
                $pesquisa_titulo = strip_tags($pesquisa_titulo);
            }

            if(isset($_POST["pesquisa_post"])){
                $pesquisa_post = trim(mysqli_real_escape_string($_conn,$_POST["pesquisa_post"]));
                $pesquisa_post = trim($pesquisa_post);
                $pesquisa_post = strip_tags($pesquisa_post);
            }

            if(!isset ($_POST["sub_categoria"])){

                if(isset($_POST["categoria"])){
                    $id_categoria_pesquisada = $_POST["categoria"];
                }
                 
            }else{ 
                $id_categoria_pesquisada = $_POST["sub_categoria"]; 
            }

        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Pesquisa</title>
        <?php include_once "style.html";?>
    </head>
    <body>
        <div class = "flex-wrapper w3-auto w3-responsive">
            <?php include_once "navbar.php";?>
            <div class="content w3-theme w3-padding-64 w3-padding-large w3-center">
                <div class="center">
                    <h1>Pesquisa</h1>
                    <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
                        <div class="w3-row-padding w3-padding-32 w3-row w3-center" style="width: 65%; margin:auto;">
                            <form action="#" method="POST">
                                <div class="w3-threequarter w3-padding-16">
                                <input class="w3-input w3-border w3-round" name="pesquisa_geral" id="pesquisa_geral" type="text" required>
                                <input name="tipo_pesquisa" id="tipo_pesquisa" type="hidden" value="simples">
                                </div>
                                <div class="w3-quarter w3-padding-16">
                                <input class="w3-btn w3-border w3-round" name="pesquisar" id="pesquisar" type="submit" value="Pesquisar">
                                </div>
                            </form> 
                        </div>

                        <button onclick="document.getElementById('popup').style.display='block'" class="w3-btn w3-border w3-round">Pesquisa Avançada</button>

                        <div id="popup" class="w3-modal w3-animate-opacity">
                            <div class="w3-modal-content w3-card-4 w3-theme w3-animate-opacity">
                                <div class="w3-container w3-section">
                                <span onclick="document.getElementById('popup').style.display='none'"
                                class="w3-button w3-display-topright">&times;</span>
                                <div class="w3-content w3-center w3-padding-large">
                                    <h4>Pesquisa Avançada</h4>
                                <div class="w3-section">
                                <form action="#" method="POST">
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

                        <div class="w3-content w3-center w3-padding-16">
                                <div class="w3-section w3-padding" id="posts_pesquisados"></div>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once "footer.php";?>
        </div>
    </body>
</html>

<script>

    var modal = document.getElementById('popup');

     window.onclick = function(event) {
    if (event.target == modal) {
    modal.style.display = "none";
    }
    }

    $(document).ready(function() {

        var tipo_pesquisa = "<?php echo $tipo_pesquisa;?>";
        var pesquisa_geral = "<?php echo $pesquisa_geral;?>";
        var pesquisa_post = "<?php echo $pesquisa_post;?>";
        var pesquisa_titulo = "<?php echo $pesquisa_titulo;?>";
        var id_categoria_pesquisada = "<?php echo $id_categoria_pesquisada;?>";
    
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

        carregar_posts();

        function carregar_posts(){

        $.ajax({
            url: "buscar_posts_pesquisados.php",
            data: {
                tipo_pesquisa: tipo_pesquisa,
                pesquisa_geral: pesquisa_geral,
                pesquisa_post:  pesquisa_post,
                pesquisa_titulo: pesquisa_titulo,
                id_categoria_pesquisada: id_categoria_pesquisada
            }, 
            method:"POST",
            
            success:function(dados){

            $('#posts_pesquisados').html(dados)

            }
        })

        }

    });

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
