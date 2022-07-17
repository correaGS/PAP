<?php 

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
// estabelecer conexão à base de dados

include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

$mensagemErroCodigo = "";
$mensagemErroSenha = "";
$senha = "";
$codigo = "";


if ( isset($_POST['botao-cancelar-entrada']) ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if ( isset($_POST['botao-esqueci-senha']) ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: userRecuperarSenha.php");
}



if ( isset($_POST['botao-iniciar-sessao']) ) {
    
    $codigo = strtolower(trim(mysqli_real_escape_string($_conn,$_POST["formCodigo"])));
    $codigo = trim($codigo);
    
    $senha = trim(mysqli_real_escape_string($_conn,$_POST["formSenha"]));
    $senha = trim($senha);
    
    $codigo = strip_tags($codigo);
    $sql = userVerification();
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('ss', $codigo, $codigo);
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
            
            if ($rowUsers['status']==3) { // utilizador bloqueado

                    $mensagemErroSenha="Não é possível entrar no sistema. Contacte os nossos serviços para obter ajuda.";
                     
                    } else  if ($rowUsers['status']==1 ) { // Utilizador criou a conta mas não ativou

                                 $mensagemErroSenha=  $rowUsers['nome'] . ", ainda não ativou a sua conta. A mensagem com o código inicial de ativação de conta foi enviada para a sua caixa de correio. Caso não a encontre na sua caixa de entrada, verifique também o seu correio não solicitado ou envie-nos um email para ativarmos a sua conta. Obrigado."; 

                            } else  if ( password_verify($senha, $rowUsers['password'])) {
           
            
                $_SESSION["UTILIZADOR"]=$rowUsers["username"];
                $_SESSION["NIVEL_UTILIZADOR"]=$rowUsers["nivel"];
                $_SESSION["NOME_UTILIZADOR"]= $rowUsers["nome"];
                $_SESSION["EMAIL_UTILIZADOR"]= $rowUsers["email"];
                
              
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                header("Location: index.php");
            } else {
                $mensagemErroSenha = "Senha incorreta!";
                
                
                // encaminhar para página principal
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                
                //header("Refresh: 3; URL=index.php"); // encaminhar 5 segundos depois
                
                
                
            }
   
            
            
        }
    } else {
        $mensagemErroCodigo = "O código de utilizador não existe na nossa base de dados!";
    }
    
    $stmt->free_result();
    $stmt->close();
    
}



 
?>
<!DOCTYPE html>
<html>
<title>Login</title>
<?php include_once "style.html";?>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center">
<div class="center">
      <h1>Login</h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
      <form form action="#" method="POST" >
        <div class="w3-section">
          <label>Utilizador</label><p><?php echo $mensagemErroCodigo;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="text" name="formCodigo" value="<?php echo $codigo;?>" required placeholder="Username/Email">
        </div>
        <div class="w3-section">
          <label>Senha</label><p><?php echo $mensagemErroSenha;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="password" name="formSenha" value="<?php echo $senha;?>" required placeholder="Senha">
        </div>
        <button name="botao-iniciar-sessao" type="submit" class="w3-btn">Entrar</button>
        <button form="cancelar" name="botao-cancelar-entrada" type="submit" class="w3-btn">Cancelar</button>
        <button form="esqueci" name="botao-esqueci-senha" type="submit" class="w3-btn">Recuperar Senha</button>
        <button type="button" class="w3-btn" onClick="location.href='userCriarConta.php'">Ainda não tenho conta</button>
      </form>
      <form id="cancelar" action="#" method="POST">
      </form>
      <form id="esqueci" action="#" method="POST">
      </form>
        </div>
</div>
</div>
<?php include_once "footer.php";?>
</div>
</body>
</html>
