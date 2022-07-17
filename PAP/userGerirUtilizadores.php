<?php 

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

if ($_SESSION["NIVEL_UTILIZADOR"]!=3) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}


// manter o critério de pesquisa

if ( isset($_POST["filtroSQL"]))  {
    
    $filtroSQL = $_POST["filtroSQL"];
    
    if ( trim($filtroSQL)=='') {
        $filtroSQL = pesquisaUserASC();
    }
    
}  else {
    $filtroSQL = pesquisaUserDESC();
}

if ( $_SESSION["NIVEL_UTILIZADOR"]==3) {
    
    if ( isset($_POST["botao-ordenar-users-asc"])  ) {
        
        $filtroSQL = pesquisaUserASC();
    }
    if ( isset($_POST["botao-ordenar-users-desc"])  ) {
        
        $filtroSQL = pesquisaUserDESC();
    }
}

if ( $_SESSION["NIVEL_UTILIZADOR"]==3) {
    
    if ( isset($_POST["botao-ordenar-users-nome-asc"])  ) {
        
        $filtroSQL = pesquisaNomeASC();
    }
    if ( isset($_POST["botao-ordenar-users-nome-desc"])  ) {
        
        $filtroSQL = pesquisaNomeDESC();
    }
}



$campoPesquisa = "";
if ( isset($_POST['botao-pesquisar-lista-utilizadores'])) {
    
    $campoPesquisa = trim(mysqli_real_escape_string($_conn,$_POST['campoPesquisa']));
    
    if ( trim($campoPesquisa)!="") {
        
        $filtroSQL = pesquisa($campoPesquisa);;
    }
    
}



if ( isset($_POST["botao-ativar-utilizador"])  ) {
    
    // fazer update à tabela de USERS para atualizar o estado e limpar o token
    $sql= ativarConta();
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        
        $codigoAtivar = $_POST["codigoAtivar"];
        
        mysqli_stmt_bind_param($stmt, "s", $codigoAtivar);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        
        
    } else {

        mysqli_stmt_close($stmt);
        // falhou a atualização
       
        echo "STATUS ADMIN (ativar utilizador manualmente): " . mysqli_error($_conn);
    }
    
    
    
    
}


if ( isset($_POST["botao-bloquear-utilizador"])  ) {
    
    // fazer update à tabela de USERS para atualizar o estado e limpar o token
    $sql= bloquearConta();
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        
        $codigoAtivar = $_POST["codigoAtivar"];
        
        mysqli_stmt_bind_param($stmt, "s", $codigoAtivar);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        
        
    } else {

        $stmt->close();
        // falhou a atualização
        
        echo "STATUS ADMIN (ativar utilizador manualmente): " . mysqli_error($_conn);
    }
    
    
    
    
}

if ( isset($_POST["botao-desbloquear-utilizador"])  ) {
    
    // fazer update à tabela de USERS para atualizar o estado e limpar o token
    $sql= ativarConta();
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        
        $codigoAtivar = $_POST["codigoAtivar"];
        
        mysqli_stmt_bind_param($stmt, "s", $codigoAtivar);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        
        
    } else {

        mysqli_stmt_close($stmt);
        // falhou a atualização
        
        echo "STATUS ADMIN (ativar utilizador manualmente): " . mysqli_error($_conn);
    }
    
    
    
    
}


if(isset($_POST["botao-exportar-contactos"])){
    
        
        $delimiter = ";";
        $filename = "Utilizadores_registados" . "_" . date('Y-m-d') . ".csv";
        
        //create a file pointer
        $f = fopen('php://memory', 'w');
        
        
        //
        //set column headers
        $fields = array('Nome', 'Email', 'Hora de registo');
        fputcsv($f, $fields, $delimiter);
        
        $query = $filtroSQL;
        
        $result = mysqli_query($_conn, $query);
        while($row = mysqli_fetch_assoc($result))
        {
            
            $nomeCSV = $row["nome"];
            $nomeCSV= iconv("UTF-8","ISO-8859-1",$nomeCSV);
            
           
            $emailCSV = $row["email"];
            
            
            $lineData = array($nomeCSV, $emailCSV, $row['data_hora']);
            
            
            fputcsv($f, $lineData, $delimiter);
        }
        
        
        //move back to beginning of file
        fseek($f, 0);
        
        //set headers to download file rather than displayed
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '";');
        
        //output all remaining data on a file pointer
        fpassthru($f);
        
        fclose($f);
        exit;

        

  
}



    
// // saber total de utilizadores
//$stmt = $_conn->prepare('qtdUsers()'); 
//$UTILIZADORES_TOTAL = 0;
//$stmt->execute();

//     $resultadoTotal = $stmt->get_result();
//    
//    if ($resultadoTotal->num_rows > 0) {
//         while ($rowTotal = $resultadoTotal->fetch_assoc()) {
//             $UTILIZADORES_TOTAL = $rowTotal["total"];
//         }
//     }
//     mysqli_stmt_close($stmt);
$query = qtdUsers();
 $resultadoTotal = mysqli_query($_conn, $query);           

 $UTILIZADORES_TOTAL = 0;
 if (mysqli_num_rows($resultadoTotal) > 0) {
      while($rowTotal = mysqli_fetch_assoc($resultadoTotal)) {
           $UTILIZADORES_TOTAL = $rowTotal["total"];
      }
 }
 $resultadoTotal->free_result();


    

?>
<!DOCTYPE html>
<html>
<head>
<title>Gerir utilizadores</title>
<?php include_once "style.html";?>
</head>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center w3-container">
  <div class="center">
      <h1>Gerir Utilizadores</h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
<i  id="ancoraTopo"><br><br></i>     

<form action="userGerirUtilizadores.php#ancoraTopo" method="POST" >
        <div class="w3-section">
          <b><?php echo $UTILIZADORES_TOTAL;?> utilizador(es) na base de dados.</b>
          <button type="submit" name="botao-refresh-users-asc" class="w3-btn"> Atualizar</button>
          <input  id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">
        </div>
        </form>
        <form action="userGerirUtilizadores.php#ancoraTopo" method="POST">
            <div class="w3-section">     
                Pesquisar código, nome, e-mail ou data/hora:&nbsp;  
                <input type="text" name="campoPesquisa" class="w3-border w3-round-large" value="<?php echo $campoPesquisa;?>">
                <button name="botao-pesquisar-lista-utilizadores" type="submit" class="w3-btn">Pesquisar</button>
            </div>
        </form> 
<br>

  <form action="userGerirUtilizadores.php#ancoraTopo" method="POST">
                    
                <button name="botao-exportar-contactos" type="submit" class="w3-btn">Exportar contactos (CSV)</button>
                <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>"> 
  </form>   





<i  id="ancoraTopo"><br><br></i>     
 
<div class="w3-responsive" style="max-width: 70vw; margin-left:auto; margin-right:auto;">
     
     <table class="w3-table-all w3-card w3-large w3-hoverable">
      <tr>
        <th>Código<br>
        <form  action="userGerirUtilizadores.php#ancoraTopo" method="POST">
                       <i class="material-icons" style="font-size:24px;vertical-align:middle;">sort_by_alpha</i><BR>
                        <button type="submit" name="botao-ordenar-users-asc" class="w3-button"><i class="material-icons" style="font-size:24px;vertical-align:middle;">arrow_drop_up</i></button>
                        <button type="submit" name="botao-ordenar-users-desc" class="w3-button"><i class="material-icons" style="font-size:24px;vertical-align:middle;">arrow_drop_down</i></button>
                        
                  </form></th>

        
        <th>Situação</th>
        <th>Nome<br>
        
                  <form  action="userGerirUtilizadores.php#ancoraTopo" method="POST">
                       <i class="material-icons" style="font-size:24px;vertical-align:middle;">sort_by_alpha</i><BR>
                        <button type="submit" name="botao-ordenar-users-nome-asc" class="w3-button"><i class="material-icons" style="font-size:24px;vertical-align:middle;">arrow_drop_up</i></button>
                        <button type="submit" name="botao-ordenar-users-nome-desc" class="w3-button"><i class="material-icons" style="font-size:24px;vertical-align:middle;">arrow_drop_down</i></button> 
                  </form>
        
        
        </th>
        <th>Email</th>
        <th>Data de registo</th>
      </tr>
     
     
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
                
                <?php  if ($rowTabela["status"]==1) { ?><i class="material-icons w3-text-red" style="font-size:24px;vertical-align:middle;">person</i><?php } ?>
                <?php  if ($rowTabela["status"]==2) { ?><i class="material-icons w3-text-green" style="font-size:24px;vertical-align:middle;">how_to_reg</i><?php } ?>
                <?php  if ($rowTabela["status"]==3) { ?><i class="material-icons w3-text-red" style="font-size:24px;vertical-align:middle;">voice_over_off</i><?php } ?>
                  
                <?php  if ($rowTabela["marketing"]==1) { ?><i class="material-icons w3-text-red" style="font-size:24px;vertical-align:middle;">notifications_off</i><?php } ?>
                <?php  if ($rowTabela["visibilidade"]==2) { ?><i class="material-icons w3-text-red" style="font-size:24px;vertical-align:middle;">public_off</i><?php } ?>
                
                
                 <?php  if ($rowTabela["status"]==1) { ?>
                            <form  action="userGerirUtilizadores.php#ancoraUtilizador<?php echo ($ctd);?>" method="POST">
                                <i class="material-icons w3-text-green" style="font-size:24px;vertical-align:middle;" >subdirectory_arrow_right</i>
                                   <button type="submit" name="botao-ativar-utilizador" class="w3-button w3-text-green"><i class="material-icons" style="font-size:24px;vertical-align:middle;">how_to_reg</i></button>
                                   <input id="codigoAtivar" name="codigoAtivar" type="hidden" value="<?php echo $rowTabela["username"]; ?>">
                                 
                                   <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">  
                            </form> 
                    <?php } ?>
                    
                     <?php  if ($rowTabela["status"]==2) { ?>
                            <form  action="userGerirUtilizadores.php#ancoraUtilizador<?php echo ($ctd);?>" method="POST">
                                <i class="material-icons w3-text-grey" style="font-size:24px;vertical-align:middle;" >subdirectory_arrow_right</i>
                                   <button type="submit" name="botao-bloquear-utilizador" class="w3-button w3-text-grey"><i class="material-icons" style="font-size:24px;vertical-align:middle;">voice_over_off</i></button>
                                   <input id="codigoAtivar" name="codigoAtivar" type="hidden" value="<?php echo $rowTabela["username"]; ?>">
                                 
                                   <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">  
                            </form> 
                    <?php } ?>
                    
                     <?php  if ($rowTabela["status"]==3) { ?>
                            <form  action="userGerirUtilizadores.php#ancoraUtilizador<?php echo ($ctd);?>" method="POST">
                                <i class="material-icons w3-text-grey" style="font-size:24px;vertical-align:middle;" >subdirectory_arrow_right</i>
                                   <button type="submit" name="botao-desbloquear-utilizador" class="w3-button w3-text-grey"><i class="material-icons" style="font-size:24px;vertical-align:middle;">how_to_reg</i></button>
                                   <input id="codigoAtivar" name="codigoAtivar" type="hidden" value="<?php echo $rowTabela["username"]; ?>">
                                 
                                   <input id="filtroSQL" name="filtroSQL" type="hidden" value="<?php echo $filtroSQL; ?>">  
                            </form> 
                    <?php } ?>
                    
                    
                                    
            </td>
		    
			<td><b> <?php echo $rowTabela["nome"]?></b><?php  if ($rowTabela["nivel"]==3) { echo "<br>(Administrador)";}?></td>                  
			<td><?php echo $rowTabela["email"]?></td>                  
			<td>[<?php echo $rowTabela["data_hora"]?>]</td>        

            
			</tr>

 	<?php
            }
        }
        //mysqli_close($resultadoTabela);
        mysqli_free_result($resultadoTabela);
        //$resultadoTabela->next_result();
       
    ?>
    </table>
    </div>
    <br><br>
    <form action="index.php" method="POST">
        <input type="submit" class="w3-btn w3-xlarge" value="Voltar">
      </form>
    </div>
    </div>
</div>
    <?php include_once "footer.php";?>
</div>
</body>
</html>
