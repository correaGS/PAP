<?php 

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';


// manter o critério de pesquisa
if ( !isset($_SESSION["UTILIZADOR"])) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
} else {

if ( isset($_POST["filtroSQL"]))  {
    
    $filtroSQL = $_POST["filtroSQL"];
    
    if ( trim($filtroSQL)=='') {
        $filtroSQL = pesquisaUserPublico();
    }
    
}  else {
    $filtroSQL = pesquisaUserPublico();
}

$campoPesquisa = "";
if ( isset($_POST['botao-pesquisar-lista-utilizadores'])) {
    
    $campoPesquisa = trim(mysqli_real_escape_string($_conn,$_POST['campoPesquisa']));
    
    if ( trim($campoPesquisa)!="") {
        
        $filtroSQL = pesquisaPublico($campoPesquisa);;
    }
    
}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Perfis Públicos</title>
<?php include_once "style.html";?>
<body>
<?php include_once "navbar.php";?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
      <h1>Perfis Públicos</h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
<i  id="ancoraTopo"><br><br></i>     

<form action="perfis.php#ancoraTopo" method="POST" >
        <div class="w3-section">
          <button type="submit" name="botao-refresh-users-asc" class="w3-btn"> Atualizar</button>
          <input  id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">
        </div>
        </form>
        <form action="perfis.php#ancoraTopo" method="POST">
            <div class="w3-section">     
                Pesquisar Utilizador:&nbsp;  
                <input type="text" class="w3-border w3-round-large" name="campoPesquisa" value="<?php echo $campoPesquisa;?>">
                <button name="botao-pesquisar-lista-utilizadores" type="submit" class="w3-btn">Pesquisar</button>
            </div>
        </form> 
<br>

<i  id="ancoraTopo"><br><br></i>     
 
<div class="w3-responsive">
     
     <table class="w3-table-all w3-card w3-large w3-hoverable" style="width:40%; margin-left:auto; margin-right:auto;">

     <?php 
        

        $resultadoTabela = mysqli_query($_conn, $filtroSQL);           
        if (mysqli_num_rows($resultadoTabela) > 0) {
            $ctd = 0;
            while($rowTabela = mysqli_fetch_assoc($resultadoTabela)) {
                $ctd=$ctd+1;
            ?>   

			<tr>
   			 
            <td id ="ancoraUtilizador<?php echo $ctd;?>"><b><?php echo $rowTabela["username"]?></b></td>

            <td>
            <form  action="verPerfil.php" method="GET">
                                   <input type="submit" class="w3-button w3-theme w3-right" value="Ver Perfil">
                                   <input id="id" name="id" type="hidden" value="<?php echo $rowTabela["username"]; ?>">
                            </form> 
            </td>
			</tr>

 	<?php
            }
        }

        mysqli_free_result($resultadoTabela);
       
    ?>
    </table>
    </div>
    <br><br>
    <form action="index.php" method="POST">
        <input type="submit" class="w3-btn w3-xlarge" value="Voltar">
      </form>
    </div>
    </div>
    <?php include_once "footer.php";?>
</body>
</html>
