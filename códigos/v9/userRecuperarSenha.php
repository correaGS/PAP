<?php 


session_start();
error_reporting(E_ERROR | E_PARSE);
include_once  'conexaobasedados.php';
include_once 'comandosbasedados.php';
include_once 'function_mail_utf8.php'; 

$mensagemErroCodigo = "";
$mensagemErroSenha = "";
$codigo = "";
$mensagemEmail = "";
$geraFormulario = "Sim";

if ( isset($_POST['botao-voltar']) ) {
    
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if ( isset($_POST['botao-recuperar-senha']) ) {
    
    $codigo = mysqli_real_escape_string($_conn, $_POST['formCodigo']);
    $codigo = strtolower(trim($codigo));
    $sql = userVerification();
    $stmt = $_conn->prepare($sql);;
    $stmt->bind_param('ss', $codigo, $codigo);
        $stmt->execute();

        $resultadoUsers = $stmt->get_result();

        if ($resultadoUsers->num_rows > 0) {
            while ($rowUsers = $resultadoUsers->fetch_assoc()) { 
                
                $nome = $rowUsers['nome'];

                if ($rowUsers['status']==3) { // utilizador bloqueado

                        $mensagemErroSenha="Não foi enviada mensagem de recuperação de senha, contacte os nossos serviços para obter ajuda.";

                        } else  if ($rowUsers['status']==1 ) { // Utilizador criou a conta mas não ativou

                                     $mensagemErroSenha=  $rowUsers['nome'] . ", ainda não ativou a sua conta. A mensagem com o código inicial de ativação de conta foi enviada para a sua caixa de correio. Caso não a encontre na sua caixa de entrada, verifique também o seu correio não solicitado ou envie-nos um email para ativarmos a sua conta. Obrigado."; 

                                } else   {

                // Recuperar a senha 
                // gerar token, preparar e enviar mail de recuperação
                
                $stmt->free_result();
                $stmt->close();
                
                $code = md5(uniqid(rand()));
                

                $sql= updateToken();
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                                   
                    mysqli_stmt_bind_param($stmt, "ss", $code, $codigo);
                    mysqli_stmt_execute($stmt);
                    
                    // /////////////////////////////////////////////////////////////////////////////////////////////////
                    // Update efetuado com sucesso, preparar e enviar mensagem /////////////////////////////////////////
                    $id = base64_encode($codigo);
                    
                    $urlPagina = "http://localhost/testes/PAP/";
                  
                    
                    $mensagem = "Caro(a) $nome" . "," . "\r\n" .  "\r\n" .

                            
                        "Foi-nos pedido para recuperar a sua senha. Se nos pediu isto basta seguir as instruções seguintes, caso contrário, ignore esta mensagem.". "\r\n" .  "\r\n" .

                        "Para recuperar agora a sua senha basta carregar na seguinte ligação:" ."\r\n" ."\r\n" .

                        $urlPagina . "userNovaSenha.php?id=$id&code=$code" ."\r\n" ."\r\n" . 

                        "Esta mensagem foi-lhe enviada automaticamente.";  

                    $subject = "Recuperação da sua senha em $urlPagina";

                    // use wordwrap() if lines are longer than 70 characters
                    $mensagem = wordwrap($mensagem,70);

                    // send email
                    mail_utf8($email,$subject,$mensagem); 
                    echo $mensagem; // apenas para efeitos de teste...
                           
                           
                    //$msgTemporaria = $email . " " . $subject . " " . $mensagem;
                    // mail enviado
                   
                    $mensagemEmail= " " . "$nome, verifique por favor a sua caixa de correio para recuperar de imediato a sua senha!";
                   
                    
                    //
                    // fim do envio de mensagem //////////////////////////////////////////////////////////////////            
                        
                    $geraFormulario = "Nao";
    
                                    
                } else {
                    // erro
                    echo "STATUS ADMIN (recuperar senha): " . mysqli_error($_conn);
                }                  
                                    
                    
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

<head>
<title>Recuperar senha</title>
<?php include_once "style.html";?>
<body>
<?php include_once "navbar.php";?>
<?php 

   if ( $geraFormulario == "Sim" ) { 
   
?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
    <h2>Recuperar Senha</h2>
    <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
    <form action="#" method="POST">
               
          
               <p>Introduza o código que utiliza para inciar sessão e carregue no botão para recuperar senha. Será enviada para o seu e-mail uma mensagem com um código de recuperação de senha.
               <br> Por vezes estas mensagens são consideradas correio não solicitado. Se não vir a mensagem de recuperação verifique o seu correio não solicitado (SPAM).</p>
               <br><p> </p>
               <div class="w3-section">
            <label>Código de utilizador para recuperação de senha</label><p><?php echo $mensagemErroCodigo;?></p>
            <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="text" name="formCodigo" value="<?php echo $codigo;?>" required placeholder="Username/Email">
            </div>
               <button name="botao-recuperar-senha" type="submit" class="w3-btn"> Recuperar senha agora</button>
               <button form="voltar" name="botao-voltar" type="submit" class="w3-btn">Voltar</button>
      </form>
      <form id="voltar" action="#" method="POST">
    </form>
    </div>
</div> 

<?php 
   } else { 
?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
<h2>Recuperação de senha Aceita</h2>
    <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
   <p>
   <br>Link de recuperação enviado. Verifique a sua caixa de correio.
   </p>
   <p><?php echo $mensagem;?></p>
   <?php 
   
   // encaminhar para página principal
   header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
   header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
   
   // Comentado para efeitos de teste (copy/paste do link de recuperação)
   // header("Refresh: 5; URL=index.php"); // encaminhar 5 segundos depois
           
   ?>
       <form action="#" method="POST">
    <button name="botao-voltar" type="submit" class="w3-btn w3-xlarge w3-xlarge">Voltar</button>
    </form>
   </div>
   </div>
<?php
   }   		
?>

<?php include_once "footer.php";?>        
</body>
</html>
