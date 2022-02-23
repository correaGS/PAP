<?php 

session_start();
error_reporting(E_ERROR | E_PARSE);

include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

if (empty($_GET['id']) || empty($_GET['code'])) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
     
}

$codigo ="";
$senha ="";
$senhaConfirmacao ="";
$nome ="";


$mensagem ="";

$geraForm = "Nao"; 
$interfaceSenhas = "Nao";
$sucesso = "Nao";

if (isset($_GET['id']) && isset($_GET['code'])) {
    
    
    $codigo = base64_decode($_GET['id']); 
    $code = $_GET['code'];
    $sql = recuperarSenha();
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('ss', $codigo, $code); 
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
             $geraForm="Sim";  
             $interfaceSenhas = "Sim";
             $nome = $rowUsers["nome"];
        }            
            
        } else {
           
           $mensagem = "O código de utilizador ou código de recuperação já foi utilizado ou danificado. Em caso de dificuldade, pode solicitar novamente a recuperação de senha.";
        } 
    
    mysqli_stmt_close($stmt);
   
           
}



if (isset($_POST['botao-guardar-nova-senha'])) {

    $podeAlterar ="Sim"; 
    
    $senha = $senha=mysqli_real_escape_string($_conn,$_POST["senha"]);
    $senha = trim($senha);
    
    $senhaConfirmacao = $senhaConfirmacao=mysqli_real_escape_string($_conn,$_POST["senhaConfirmacao"]);
    $senhaConfirmacao = trim($senhaConfirmacao);
    
    
    if (strlen(trim($senha))<8) { 
            $mensagem="A senha tem que ter pelo menos 8 caracteres!";
         
            $podeAlterar = "Nao"; 
    }
        
    if ($senha!=$senhaConfirmacao) { 
            $mensagem="A senha de confirmação deve ser igual à primeira senha!";
          
            $podeAlterar = "Nao"; 
    }
    
    
    if ( $podeAlterar == "Sim") {
        
        ///////////////////////////////////
        // ALTERAR SENHA
        //////////
    
        $sql= updatePassword();

        if ( $stmt = mysqli_prepare($_conn, $sql)  ) {

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            mysqli_stmt_bind_param($stmt, "ss", $senhaHash, $codigo);
            
            mysqli_stmt_execute($stmt);
            
           
            $mensagem = "A sua nova senha foi alterada com sucesso." ;
              
            $interfaceSenhas = "Nao";
            
            
            $sucesso = "Sim";
            $geraForm = "Nao";
            
      

        } else{
            
            echo "STATUS ADMIN (nova senha): " . mysqli_error($_conn);
        }

       // mysqli_stmt_close($stmt);
        
    }
    
}
    
?>
<!DOCTYPE html>
<html>

<head>
<title>Definir nova senha</title>
<?php include_once "style.html";?>
<body>
<?php include_once "navbar.php";?>
<?php if ( $geraForm == "Sim") { ?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
      <h1>Alteração de senha para <?php echo $nome;?></h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
      <?php if ( $interfaceSenhas == "Sim") { ?>
                        <p><?php echo $mensagem;?></p>
      <form form action="#" method="POST" >
        <div class="w3-section">
          <label>Nova Senha</label><p><?php echo $mensagemErroCodigo;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="password" name="senha" required placeholder="Nova Senha">
        </div>
        <div class="w3-section">
          <label>Confirmar Nova Senha</label><p><?php echo $mensagemErroSenha;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="password" name="senhaConfirmacao" required placeholder="Confirmar Nova Senha">
        </div>
        <button name="botao-guardar-nova-senha" type="submit" class="w3-btn">Confirmar Nova Senha</button>
      </form>
      <?php } else { ?>
                    <p><?php echo $mensagem;?></p>
               <?php }?>
        </div>
</div>

          <?php } else { ?>
          
          <?php 
          $destino = "index.php";
          if ( $sucesso == "Nao" ) { 
          		$destino = "userRecuperarSenha.php";
          }
          
          ?>
          <div class="w3-theme w3-padding-64 w3-padding-large w3-center">
      <h1>Alteração de senha para <?php echo $nome;?></h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">

          <form action="<?php echo $destino;?>" method="POST">
              
              <p><?php echo $mensagem;?><br></p> 
              
              <?php if ( $interfaceSenhas == "Nao") { 
              
              	if ( $sucesso == "Nao" ) {
              ?>
                
                <button type="submit" class="w3-btn"> Solicitar recuperação de senha</button>
              <?php } else { ?>
              
               <button type="submit" class="w3-btn"> Voltar</button>
               
              <?php  
               
              } ?>
          </form> 
          </div>
</div> 
          <?php 
              
              } 
           }
              
              
          ?>       
<?php include_once "footer.php";?>
</body>
</html>
