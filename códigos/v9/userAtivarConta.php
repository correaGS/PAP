<?php 

session_start();
error_reporting(E_ERROR | E_PARSE);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

$mensagem ="";

if ( isset($_POST['submit-voltar']) ) {
    
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: index.php");
}

if (isset($_GET['id']) && isset($_GET['code'])) {
    
    
    $codigo = base64_decode($_GET['id']); // código do utilizador...
    $code = $_GET['code']; // token...
   
    $sql = userVerification();
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('ss', $codigo, $codigo);  
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
            
          $estado = $rowUsers['status'];
          
          if($estado!=1){
              
                $mensagem="A sua conta já se encontra ativa. Pode iniciar sessão com a sua conta."; 
          } else {
                 // Procedimento de segurança para ativar a conta...
                 if ( ($code!=$rowUsers['token'] || $rowUsers['token']=='') ) {
                      
                         $mensagem="O código de ativação não está correto ou já foi utilizado."; 
                 } else {
                    $stmt->free_result();
                    $stmt->close();
                     // o código de ativação está correto e não foi ainda utilizado
                     // fazer update à tabela de USERS para atualizar o estado e limpar o token
                     $sql= ativarConta();
                
                     if ( $stmt = mysqli_prepare($_conn, $sql) ) {
                            
                            mysqli_stmt_bind_param($stmt, "s", $codigo);
                            mysqli_stmt_execute($stmt);
                            
                        
                            $mensagem="A sua conta foi ativada com sucesso! Já pode iniciar a sua sessão."; // ok
                           
                            
                     } else {
                         // falhou a atualização 
                    
                         echo "STATUS ADMIN (ativar conta): " . mysqli_error($_conn);
                     }
                     mysqli_stmt_close($stmt);
                     
                     
                     
                 }
          }
        }            
            
        } else {
           
           $mensagem = "A conta para ativar não existe na nossa base de dados!";
        } 
    } else {
    
        // caso alguém use o endereço sem parametros volta 
        // de imediato para a página principal sem dar qualquer
        // tipo de mensagem
        
        // encaminhar para página principal
  		 header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
  		 header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
  		 header("Location: index.php"); // encaminhar de imediato

        
        
    
    }

    
?>

<!DOCTYPE html>
<html>
<title>Ativar conta</title>
<?php include_once "style.html";?>
<body>
<?php include_once "navbar.php";?>
<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
    <h2>Conta Ativada com sucesso</h2>
    <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
   
   <p><?php echo $mensagem;?></p>
   
   <?php 
   
   // encaminhar para página principal
   header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
   header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
   
   // Comentado para efeitos de teste (copy/paste do link de ativação, link voltar em html é provisºorio)
   // header("Refresh: 5; URL=index.php"); // encaminhar 5 segundos depois
              
   ?>

    <form action="#" method="POST">
    <button name="submit-voltar" type="submit" class="w3-btn w3-xlarge">Voltar</button>
    </form>
    </div>
</div>
<?php include_once "footer.php";?>   
</body>
</html>
