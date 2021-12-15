<?php 

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
// estabelecer conexão à base de dados

include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

//$mensagemErroPost = "";
$post = "";
$codigo = "";
$mensagem = "";


if ( !isset($_SESSION["UTILIZADOR"])) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if ( isset($_POST['botao-voltar']) ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if ( isset($_POST['botao-post']) ) {
    
    $post= strtolower(trim(mysqli_real_escape_string($_conn,$_POST["formPost"])));
    $post = trim($post);
    $post = strip_tags($post);

    $codigo = $_SESSION["UTILIZADOR"];
    $sql = postar();
    if($stmt = mysqli_prepare($_conn, $sql)){
    $stmt->bind_param('ss', $codigo, $post);
    $stmt->execute();
    
    $stmt->free_result();
    $stmt->close();

    $mensagem = "Post Publicado!!";
    }else{
            
    echo "STATUS ADMIN (inserir post): " . mysqli_error($_conn);
}
} 



 
?>
<!DOCTYPE html>
<html>
<title>Criar Publicação</title>
<?php include_once "style.html";?>
<body>
<?php include_once "navbar.php";?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
      <h1>Criar Publicação</h1>
      <p><?php echo $mensagem;?></p>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
      <form form action="#" method="POST" >
        <div class="w3-section">
          <label><h3>Post</h3></label>
          <textarea class="w3-input w3-border w3-round-large w3-theme-l5" style="width:100%;" rows="14" type="text" maxlength="3000" name="formPost" value="<?php echo $post;?>" required placeholder="Digite algo..."></textarea>
        </div>
        <button name="botao-post" type="submit" class="w3-btn">Publicar</button>
        <button form="voltar" name="botao-voltar" type="submit" class="w3-btn">Voltar</button>
      </form>
      <form id="voltar" action="#" method="POST">
      </form>
        </div>
</div>
<?php include_once "footer.php";?>
</body>
</html>
