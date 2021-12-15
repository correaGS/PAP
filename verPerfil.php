<?php 

session_start();
error_reporting(E_ERROR | E_PARSE);
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
            $apelido = $rowUsers['apelido'];
            $telemovel = $rowUsers['telemovel'];
                     mysqli_stmt_close($stmt);
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
<?php include_once "navbar.php";?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
    <h1>Perfil de <?php echo $_GET['id'];?></h1>
    <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
    <div class="w3-section">
        <h2>Dados</h2>
        <p><?php echo $mensagem;?></p> 
        <?php if ($perfilDisponivel == "SIM"){?>
            <div class="w3-section">
          <label> <img id="pic" name="pic" src="<?php echo $pic;?>" class="w3-center w3-image w3-circle w3-hover-opacity"> </label></p>
    </div>
    <div class="w3-section">
          <label class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;">Nome - <?php echo $nome." ".$apelido;?></label></p>
    </div>
    <div class="w3-section">
          <label class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;">Telefone - <?php echo $telemovel;?></label></p>
    </div>
    </div>
    <?php } ?>

    <?php
    $username = "";
    $post = "";
    $dataHora = "";
    $sql = verPostsUtilizador($codigo);
    $resultadoTabela = mysqli_query($_conn, $sql);           
     if (mysqli_num_rows($resultadoTabela) > 0) {
          $ctd = 0;
          echo"<div class='w3-section'><h2>Posts</h2>";
          while($rowTabela = mysqli_fetch_assoc($resultadoTabela)) {
              $ctd=$ctd+1;
              $username = $rowTabela["username"];
              $post = $rowTabela["post"];
              $post = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $post);
              $post = wordwrap($post, 120);
              $dataHora = $rowTabela["data_hora"];
           ?>
           <div class="w3-padding-16">
           <div class="w3-section w3-panel w3-round-large w3-theme-l2 w3-card-4 we-padding">
           <div class="w3-quarter w3-center">
           <p><?php echo $username?></p><p><?php echo $dataHora?></p>
           </div>
           <div class="w3-threequarter w3-center">
           <p><?php echo $post?></p>
           </div>
           </div>
          </div>
           <?php
       }
       echo"</div>";
      } mysqli_free_result($resultadoTabela);
       ?>

<form action="#" method="POST">
    <button name="submit-voltar" type="submit" class="w3-btn w3-xlarge">Voltar</button>
    </form>
    </div>
</div>
<?php include_once "footer.php";?>   
</body>
</html>
