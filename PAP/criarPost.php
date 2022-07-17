<?php 

  session_start();
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  error_reporting(E_ALL);
  // estabelecer conexão à base de dados

  include_once  'conexaobasedados.php'; 
  include_once 'comandosbasedados.php';

  //$mensagemErroPost = "";
  $post = "";
  $titulo = "";
  $codigo = "";
  $mensagem = "";
  $erro = "";


  if ( !isset($_SESSION["UTILIZADOR"])) {
    ?>
    <script type="text/javascript">
    alert("É preciso estar logado para fazer uma publicação");
    window.location = "userEntrar.php";
    </script>
    <?php
  }

  if ( isset($_POST['botao-post']) ) {
      
      $post = trim(mysqli_real_escape_string($_conn,$_POST["formPost"]));
      $post = trim($post);
      $post = strip_tags($post);

      $titulo = trim(mysqli_real_escape_string($_conn,$_POST["formTitulo"]));
      $titulo = trim($titulo);
      $titulo = strip_tags($titulo);

      if(!isset ($_POST["sub_categoria"])){ 
        $id_categoria = $_POST["categoria"]; 
      }else{ 
        $id_categoria = $_POST["sub_categoria"]; 
      }

        $codigo = $_SESSION["UTILIZADOR"];
        $sql = postar();
        if($stmt = mysqli_prepare($_conn, $sql)){
          $stmt->bind_param('sss', $codigo, $post, $titulo);
          $stmt->execute();
          $id_post_criado = mysqli_insert_id($_conn);
          $stmt->free_result();
          $stmt->close();

          $post_categoria = post_categoria();
          if($stmt = mysqli_prepare($_conn, $post_categoria)){
            $stmt->bind_param('ii', $id_post_criado, $id_categoria);
            $stmt->execute();
            $stmt->free_result();
            $stmt->close();
          }
          
          $mensagem = "Post Publicado!!";

          ?>
          <script type="text/javascript">
            window.location = "post.php?id=<?php echo $id_post_criado;?>";
          </script>
          <?php

        }else{
                
        echo "STATUS ADMIN (inserir post): " . mysqli_error($_conn);
      }
    
  }

  $categoria_sql = get_categoria();
  $resultado_categorias = mysqli_query($_conn, $categoria_sql);

?>
<!DOCTYPE html>
<html>
<title>Criar Publicação</title>
<?php include_once "style.html";?>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center w3-container">
  <div class="center">
  <h1>Criar Publicação</h1>
  <p><?php echo $mensagem;?></p>
  <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
    <form form action="#" method="POST" >
      <div class="w3-section">
      <div id="select_categoria">
        <label><h4>Categoria</h4></label>
         <select class="w3-select w3-border w3-round-large w3-margin-bottom" name="categoria" id="categoria" required>
          <option value=""  selected>Selecione uma categoria...</option>
            <?php 
              while ($row_categoria = mysqli_fetch_array($resultado_categorias)){
                ?><option value="<?php echo $row_categoria["id"]?>"><?php echo $row_categoria["descricao"]?></option><?php
              }mysqli_free_result($resultado_categorias);
            ?>
        </select>
      </div>
      <div id="select_subcategoria"></div>
        <label><h4>Titulo</h4></label>
        <input class="w3-input w3-margin-bottom w3-border w3-round-large w3-theme-l5" type="text" maxlength="3000" name="formTitulo" value="<?php echo $titulo;?>" placeholder="Digite o titulo (opcional)...">
        <label><h4>Post</h4></label>
        <textarea class="w3-input w3-margin-bottom w3-border w3-round-large w3-theme-l5" style="width:100%; overflow-x: hidden;overflow-y: scroll;" rows="14" type="text" maxlength="3000" name="formPost" value="<?php echo $post;?>" required placeholder="Digite algo..."></textarea>
        <?php echo $erro;?>
      </div>
      <button name="botao-post" type="submit" class="w3-btn">Publicar</button>
      <button name="botao-voltar" type="button" id="botao-voltar" class="w3-btn">Voltar</button>
    </form>
  </div>
  </div>
</div>

<?php include_once "footer.php";?>
</div>
</body>
</html>

<script>
  $("#botao-voltar").click(function (){
    if ('referrer' in document) {
        window.location = document.referrer;
    } else {
        window.history.back();
    }
});

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
