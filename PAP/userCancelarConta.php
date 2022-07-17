<?php 

session_start();
error_reporting(E_ERROR | E_PARSE);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';
include_once 'function_mail_utf8.php'; 


$msgTemporaria = "";
$motivo = "";
$mensagemErroMotivo = "";
$mensagemErroSenha = "";
$nome = "";
$email = "";
$pic = "";

if ( isset($_POST['botao-voltar']) ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if ( !isset($_SESSION["UTILIZADOR"])) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
} else {
    // ler definições de conta 
    
    $codigo = $_SESSION["UTILIZADOR"];
    $pic = "imagens/". $codigo . "/";
    
    $sql = userVerification(); 
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('ss', $codigo, $codigo);
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
         
            
            $senha ="";
            $senhaEncriptada =$rowUsers['password'];
            
            $nome = $rowUsers['nome'];
            
            if ( !isset($_POST["motivo"])) {
                
                // ok
                
                   
            } else {
                
                $podeApagar = "Sim"; 
                
                ///////// em modo de eliminação - filtrar e validar campos
                
                $motivo= mysqli_real_escape_string($_conn, $_POST['motivo']);
                $motivo = trim($motivo); 


                if (strlen(trim($motivo))<10) {
                     $mensagemErroMotivo="Utilize pelo menos 10 caracteres para nos indicar o seu motivo.";
                     $podeApagar = "Nao"; 
                }
                ///////////////////////////////  
            }
            
            
            
            
        }
    } else {
        echo "STATUS ADMIN (cancelar conta): " . mysqli_error($_conn);
    }           
                    
    
     $stmt->close();
    
}









if ( isset($_POST['botao-apagar-conta']) ) {
    
       
        
        
        // verificar senha por questões de segurança
      
        $senha=mysqli_real_escape_string($_conn, $_POST['senha']);
        $senha = trim($senha);
        
        
        if ( password_verify($senha, $senhaEncriptada)) {
            
            // senha OK, filtar e validar inputs
            
           
            
        } else {
            
            $mensagemErroSenha = "Senha incorreta!";
            $podeApagar = "Nao"; 
        }
        
        
        if ( $podeApagar == "Sim" )  {
            
           
                ///////////////////////////////////
                // APAGAR
                //////////////////////////////////
            
                // Tabela USERS
            
                $sql_deletar = deletarConta();
                
                if ( $stmt_deletar = mysqli_prepare($_conn, $sql_deletar) ) {
                
                    mysqli_stmt_bind_param($stmt_deletar, "s", $codigo);
                    mysqli_stmt_execute($stmt_deletar);
                    mysqli_stmt_close($stmt_deletar);
                    array_map('unlink', glob("".$pic."/*.*"));
                    rmdir($pic);
                    
                } else{
                
                    echo "STATUS ADMIN (cancelar conta - utilizador): " . mysqli_error($_conn);
                }
               
                
                //////////////////////////// 
                // ENVIAR MOTIVO POR MAIL ao utilizador
                ////////////////////////////

                $urlPagina = "http://localhost/testes/PAP/";
                
                $email =  $_SESSION["EMAIL_UTILIZADOR"];
                
                $mensagem = "$nome cancelou a sua conta" . "." . "\r\n" .  "\r\n" .

                    "E-mail: $email". "\r\n" .  "\r\n" .

                    "Registámos o motivo: $motivo" ."\r\n" ."\r\n" .

                    "Esta mensagem foi-lhe enviada automaticamente.";  

                $subject = "$nome cancelou conta em $urlPagina";

                // use wordwrap() if lines are longer than 70 characters
                $mensagem = wordwrap($mensagem,70);

                // send email
                mail_utf8($email,$subject,$mensagem);
                  
                
                
                ////////////////////////////
               
                $msgTemporaria = "A sua conta foi cancelada. Todos os seus dados foram removidos da nossa base de dados.";
                    
                // limpar variáveis de sessão
                session_destroy();

                // encaminhar com timer 3 segundos
                header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                header("Location: index.php");
             
                
        }
            
            
}


 
?>
<!DOCTYPE html>
<html>
<title>Cancelar conta</title>
<?php include_once "style.html";?>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center">
<div class="center">
    <h2>Cancelar Conta</h2>
    <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
    <form action="#" method="POST">
        <p ><?php echo $msgTemporaria;?></p>
        <p> 
            Esta opção permite cancelar a sua conta. 
            Os seus dados pessoais serão removidos definitivamente da nossa base de dados. 
            De acordo com os nossos princípios éticos de responsabilidade e transparência descritos na nossa política de privacidade,
            esta remoção inclui também dados referentes a atividades em que tenha participado nesta página. 
            Esta operação é irreversível.<br><br>
            </p>
        <div class="w3-section">
        <label>Por favor, indique-nos apenas o motivo pelo qual pretende cancelar a sua conta.
            Será tambem enviado em email para <?php echo $_SESSION["EMAIL_UTILIZADOR"];?></label><p><?php echo $mensagemErroMotivo;?></p>
        <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="text" name="motivo" value="<?php echo $motivo;?>" required placeholder="Motivo">
        </div>
        <div class="w3-section">
        <label>Utilizador <?php echo $codigo;?>, serão removidos todos os seus dados, por favor digite a sua senha para cancelar a conta.</label><p><?php echo $mensagemErroSenha;?><p>
        <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="password" name="senha" value="<?php echo $senha;?>" required placeholder="Senha">
        </div>
    
        <button name="botao-apagar-conta" type="submit"class="w3-btn"> Cancelar conta imediatamente</button>
        <button form="cancelar" name="botao-voltar" type="submit" class="w3-btn">Não pretendo cancelar a minha conta</button>
    </form>
    <form id="cancelar" action="#" method="POST">
    </form>
    <br>
    </form>
    </div>
</div>
</div>
<?php include_once "footer.php";?>
</div> 
</body>
</html>
