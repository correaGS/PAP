<?php 

session_start();
error_reporting(E_ERROR | E_PARSE);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';
include_once 'function_mail_utf8.php'; 




// iniciar e limpar possíveis mensagens de erro
$msgTemporaria = "";
$error = "";
$mensagemErroCodigo = "";
$mensagemErroEmail = "";
$mensagemErroSenha = "";
$mensagemErroSenhaRecuperacao = "";
$mensagemErroNome = "";

// inciar e limpar variáveis
$codigo="";
$email="";
$senha="";
$senhaConfirmacao="";
$nome="";
$aceito="";
$aceitoMarketing = 1;
$geraFormulario = "Sim";
$newPicPath = "";
$imagemPath = "";


if ( isset($_POST['botao-cancelar-conta']) ) {
    
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}


if ( isset($_POST['submit-criar-conta']) ) {


        $podeCriarRegisto = "Sim";
        
         
        // obter parametros (determinadas validações poderiam ser feitas no lado cliente)
        
        
        
    
        $codigo = mysqli_real_escape_string($_conn, $_POST['formCodigo']);
        $codigo = strtolower(trim($codigo));
        $email=mysqli_real_escape_string($_conn, $_POST['formEmail']);
        $email=strtolower(trim($email));
        $senha=mysqli_real_escape_string($_conn, $_POST['formSenha1']);
        $senha = trim($senha);
        $senhaConfirmacao=mysqli_real_escape_string($_conn, $_POST['formSenha2']);
        $senhaConfirmacao = trim($senhaConfirmacao);
        $nome= mysqli_real_escape_string($_conn, $_POST['formNome']);
        $nome = trim($nome);
        $aceito = $_POST['formAceito'];

        $newPicPath = 'imagens/'.$codigo.'/';
        $imagemPath = 'imagens/';
        
       
        if ( $aceito == "aceito_marketing") { 
                   $aceitoMarketing = 2;
               } else {
                   $aceitoMarketing = 1;
        }

        
        // retirar possíveis tags html do código
        $codigo = strip_tags($codigo);
        $email = strip_tags($email);
        $nome = strip_tags($nome);
        
        // não permitir que um user tenha espaços no código...
        $codigo = str_replace(' ', '', $codigo);
        
        // validar parametros recebidos

        if (strlen(trim($codigo))<4) {
            $mensagemErroCodigo="O código é demasiado curto!";
            $podeCriarRegisto = "Nao"; 
        }

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagemErroEmail="O e-mail não é válido!";
            $podeCriarRegisto = "Nao"; 
        } 


        if (strlen(trim($senha))<8) { 
            $mensagemErroSenha="A senha tem que ter pelo menos 8 caracteres!";
            $podeCriarRegisto = "Nao"; 
        }
        
        if ($senha!=$senhaConfirmacao) { 
            $mensagemErroSenhaRecuperacao="A senha de confirmação deve ser igual à primeira senha!";
            $podeCriarRegisto = "Nao"; 
        }

         
        if (strlen(trim($nome))<2) {
            $mensagemErroNome="O nome é demasiado curto!";
            $podeCriarRegisto = "Nao"; 
              
        }

        
        
        
        // a check box não precisa de ser validada..
        
        
        // inicio
        
        if ( $podeCriarRegisto == "Sim") { 
            
            // validações corretas: validar se existe utilizador
            $sql = userVerification();
            $stmt = $_conn->prepare($sql);
            $stmt->bind_param('ss', $codigo, $codigo);
            $stmt->execute();

            $resultadoUsers = $stmt->get_result();
    
            if ($resultadoUsers->num_rows > 0) {
                
                $mensagemErroCodigo = "Já existe um utilizador registado com este código/email.";
                
                $stmt->free_result();
                $stmt->close();
            }   
              
            
            else {
               
                $stmt->free_result();
                $stmt->close();

                ///////////////////////////////////
                // INSERE UTILIZADOR NA BASE DE DADOS
                //////////

                 $sql = criarConta();
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {       

                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                                
                    mysqli_stmt_bind_param($stmt, "sssi", $codigo, $email, $senhaHash, $aceitoMarketing);

                    mysqli_stmt_execute($stmt);

                    if (!file_exists($newPicPath)) {
                        mkdir($newPicPath, 0777, true);
                        copy($imagemPath.'defaultPic.png', $newPicPath.'defaultPic.png');
                        $pic = $newPicPath."defaultPic.png";
                      }

                    $sql= updateProfile();
                
                    if ( $stmt2 = mysqli_prepare($_conn, $sql) ) {
                        mysqli_stmt_bind_param($stmt2, "sss",$nome,$pic,$codigo);
                        mysqli_stmt_execute($stmt2);

                    } else{
                        $error = "STATUS ADMIN (inserir user): " . mysqli_error($_conn);
                        console_error($error);
                    }
                    mysqli_stmt_close($stmt2);

                    $geraFormulario = "Nao";
            
                } else{
            
                    $error = "STATUS ADMIN (inserir user): " . mysqli_error($_conn);
                    console_error($error);
                }
               
                mysqli_stmt_close($stmt);
                

                //////////
                // INSERIDO
                ////////////////////////////////////////
                
                ////////////////////////////////////////////////////////////////////////////
                // registo efetuado, gerar token, preparar e enviar mail de ativação
                
                $code = md5(uniqid(rand()));
                

                $sql= updateToken();
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                                   
                    mysqli_stmt_bind_param($stmt, "ss", $code,$codigo);
                    mysqli_stmt_execute($stmt);
                    
                    // /////////////////////////////////////////////////////////////////////////////////////////////////
                    // Update efetuado com sucesso, preparar e enviar mensagem /////////////////////////////////////////
                    $id = base64_encode($codigo);
                    
                    $urlPagina = "http://localhost/testes/PAP/";
                    
                    $mensagem = "Caro(a) $nome" . "," . "\r\n" .  "\r\n" .

                        "Obrigado por se ter registado.". "\r\n" .  "\r\n" .

                        "Para ativar a sua conta basta carregar na seguinte ligação:" ."\r\n" ."\r\n" .

                        $urlPagina . "userAtivarConta.php?id=$id&code=$code" ."\r\n" ."\r\n" . 

                        "Esta mensagem foi-lhe enviada automaticamente.";  

                    $subject = "Ativação da sua conta em $urlPagina";

                    // use wordwrap() if lines are longer than 70 characters
                    $mensagem = wordwrap($mensagem,70);

                    // send email
                    mail_utf8($email,$subject,$mensagem); 
                    //echo $mensagem; // apenas para efeitos de teste...
                    // 
                    //$msgTemporaria = $email . " " . $subject . " " . $mensagem;
                    // mail enviado
                    $msgTemporaria= $msgTemporaria . " " . "$nome, verifique por favor a sua caixa de correio para ativar de imediato a sua conta! Por vezes estas mensagens são consideradas correio não solicitado. Se não vir a mensagem de ativação verifique o seu correio não solicitado (SPAM).";
            
                    //
                    // fim do envio de mensagem //////////////////////////////////////////////////////////////////
                    
                } else{
                    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($_conn);
                    echo "STATUS ADMIN (gerar token): " . mysqli_error($_conn);
                }
               
                mysqli_stmt_close($stmt);
                // mail de ativação enviado
                /////////////////////////////////////////////////////////////////////////////
                
                    
                    
                    
            } 
                
        }

        
        // fim
    
} 

?>

<!DOCTYPE html>
<html>
<title>Criar conta</title>
<?php include_once "style.html";?>
<body>
<?php include_once "navbar.php";?>
<?php 
      if ($geraFormulario == "Sim") {  
?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
      <h1>Criar Conta</h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
      <form form action="#" method="POST">
        <div class="w3-section">
          <label>Utilizador</label><p><?php echo $mensagemErroCodigo;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;"type="text" id="formCodigo" name="formCodigo" value="<?php echo $codigo;?>" required placeholder="Username">
        </div>
        <div class="w3-section">
          <label>Email</label><p><?php echo $mensagemErroEmail;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="email" id="formEmail" name="formEmail" value="<?php echo $email;?>" required placeholder="Email">
        </div>
        <div class="w3-section">
          <label>Nome</label><p><?php echo $mensagemErroNome;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;"type="text" id="formNome" name="formNome" value="<?php echo $nome;?>" required placeholder="Nome">
        </div>
        <div class="w3-section">
          <label>Senha</label><p><?php echo $mensagemErroSenha;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="password" id="formSenha1" name="formSenha1" value="<?php echo $senha;?>" required placeholder="Senha">
        </div>
        <div class="w3-section">
          <label>Confirmar Senha</label><p><?php echo $mensagemErroSenhaRecuperacao;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="password" id="formSenha2" name="formSenha2" value="<?php echo $senhaConfirmacao;?>" required placeholder="Confirmar Senha">
        </div>
        <div class="w3-section">
          <input type="checkbox" class="w3-check" id="formAceito" name="formAceito" value="aceito_marketing" <?php if ($aceitoMarketing == 2 ) { echo " checked"; } ?>>
          <label for="formAceito"> Aceito que os meus dados sejam utilizados para efeitos de marketing</label>
        </div>
        <input class="w3-btn" type="submit" value="Criar Conta" id="submit-criar-conta" name="submit-criar-conta">
        <button form="cancelar" name="botao-cancelar-conta" type="submit" class="w3-btn">Cancelar</button>
      </form>
      <form id="cancelar" action="#" method="POST">
      </form>
      </div>
</div>

<?php 
   } else { 
?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
    <h2>Conta criada com sucesso</h2>
    <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
   
   <p>
   <br>
   <?php echo $msgTemporaria;?>
   </p>
   <p><?php echo $mensagem;?></p>
   
   
   <?php 
   
   // encaminhar para página principal
   header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
   header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
   
   // Comentado para efeitos de teste (copy/paste do link de ativação, link voltar em html é provisºorio)
   // header("Refresh: 5; URL=index.php"); // encaminhar 5 segundos depois
   ?>

    <form action="#" method="POST">
    <button name="submit-cancelar-conta" type="submit" class="w3-btn w3-xlarge">Voltar</button>
    </form>
</div>
   </div>
<?php
   }   		
?>
<?php include_once "footer.php";?>
</body>
</html>


