<?php 

session_start();
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

if ( isset($_POST['botao-voltar']) ) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
}
if ( isset($_POST['botao-cancelar-minha-conta']) ) {
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
    header("Location: userCancelarConta.php");
}

$msgTemporaria = "";
$mensagemErroNome = "";
$mensagemErroApelido = "";
$mensagemErroTelemovel = "";
$mensagemErroSenha = "";
$mensagemErroFoto = "";



if ( !isset($_SESSION["UTILIZADOR"])) {
     header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
     header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
     header("Location: index.php");
} else {
    // ler informações de conta 
    
    $codigo = $_SESSION["UTILIZADOR"];
    $sql = userVerification();
    $stmt = $_conn->prepare($sql);
    $stmt->bind_param('ss', $codigo, $codigo);
    $stmt->execute();

    $resultadoUsers = $stmt->get_result();
    
    if ($resultadoUsers->num_rows > 0) {
        while ($rowUsers = $resultadoUsers->fetch_assoc()) {
         
            
            $senha ="";
            $senhaEncriptada =$rowUsers['password'];
            $pic = $rowUsers['foto'];
            
            if ( !isset($_POST["nome"])) {
                
                $nome = $rowUsers['nome'];
                $visivel = $rowUsers['visibilidade'];
                $receberMsgs = $rowUsers['marketing'];        
                $apelido = $rowUsers['apelido'];
                $telemovel = $rowUsers['telemovel'];
                                
            }

             else {
                
                $podeRegistar = "Sim"; 
                
                ///////// em modo de alteração - filtrar e validar campos
                
                $nome = mysqli_real_escape_string($_conn, $_POST['nome']);
                $nome = trim($nome);

                $apelido= mysqli_real_escape_string($_conn, $_POST['apelido']);
                $apelido = trim($apelido); 

                $telemovel= mysqli_real_escape_string($_conn, $_POST['telemovel']);
                $telemovel = trim($telemovel); 

                $target_dir = "imagens/". $codigo . "/";
                $target_file = $target_dir . basename($_FILES["file"]["name"]);
                $podeRegistar = "Sim";
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
             
                $receberMensagens=$_POST['receberMensagens'];
                if ( $receberMensagens == "Sim") { 
                    $receberMsgs = 2;
                } else {
                    $receberMsgs = 1;
                }

                $visivel=$_POST['visibilidade'];
                if ( $visivel == "Sim") { 
                    $visivel = 2;
                } else {
                    $visivel = 1;
                }

                if (strlen(trim($nome))<2) {
                    $mensagemErroNome="O nome é demasiado curto!";
                    $podeRegistar = "Nao"; 
                }
                
                if(!empty($apelido)){
                if (strlen(trim($apelido))<2) {
                    $mensagemErroApelido="O apelido é demasiado curto!";
                    $podeRegistar = "Nao"; 
                }}

                if(!empty($telemovel)){
                if(!preg_match("/^[0-9]{3}[0-9]{3}[0-9]{3}[0-9]{3}$/", $telemovel)) {
                    $mensagemErroTelemovel="Número de telemovel invalido";
                    $podeRegistar = "Nao"; 
                }}

                if(!empty($_FILES["file"])){
                $check = getimagesize($_FILES["file"]["tmp_name"]);
                if($check !== false) {
                    $podeRegistar = "Sim";
                } else {
                    $mensagemErroFoto = "File is not an image.";
                  $podeRegistar = "Nao";
                }
              }
              
              // Check file size
              if ($_FILES["file"]["size"] > 500000) {
                $mensagemErroFoto = "Sorry, your file is too large.";
                $podeRegistar = "Nao";
              }
              
              // Allow certain file formats
              if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
              && $imageFileType != "gif" ) {
                $mensagemErroFoto = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $podeRegistar = "Nao";
              }
            }

              
              ///////////////////////////////  
            
            
            
            
            
        }
    } else {
        echo "STATUS ADMIN (Editar conta): " . mysqli_error($_conn);
    }           
                    
    
     mysqli_stmt_close($stmt);
    
}

if ( isset($_POST['botao-gravar-alteracoes']) ) {
    
        
        // verificar senha por questões de segurança
      
        $senha=mysqli_real_escape_string($_conn, $_POST['senha']);
        $senha = trim($senha);
        
        
        if ( password_verify($senha, $senhaEncriptada)) {
            
            // senha OK, filtar e validar inputs
            
           
            
        } else {
            
            $mensagemErroSenha = "Senha incorreta!";
            $podeRegistar = "Nao"; 
        }
        
        
        if ( $podeRegistar == "Sim" )  {
            
           
                ///////////////////////////////////
                // ALTERA
                //////////////////////////////////
                
            
                $nome = strip_tags($nome); // demonstração da remoção de caracteres especiais html por exemplo..
            
                $sql= alterarPerfil();
                
                if ( $stmt = mysqli_prepare($_conn, $sql) ) {

                    // Check if file already exists
                    if (!file_exists($target_file)) {
                        unlink(realpath($pic));
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                            echo "The file ". htmlspecialchars( basename( $_FILES["file"]["name"])). " has been uploaded.";
                            $newPicPath=$target_file;
                          } else {
                            echo "Sorry, there was an error uploading your file.";
                          }
                    }else{$newPicPath = $pic;}
                
                    mysqli_stmt_bind_param($stmt, "sssiss", $nome, $apelido, $telemovel, $visivel, $newPicPath ,$codigo);
                    mysqli_stmt_execute($stmt);
                    
                    $sql= alterarMarketing();
                
                if ( $stmt2 = mysqli_prepare($_conn, $sql) ) {
                    mysqli_stmt_bind_param($stmt2, "is",$receberMsgs, $codigo);
                    mysqli_stmt_execute($stmt2);

                    $msgTemporaria = "Definições de conta alteradas com sucesso.";
                    
                    // atualizar variável de sessão, a questão de receber mensagens de marketing não
                    // é uma variável de sessão, não é necessário guardar em sessão.
                    
                    $_SESSION["NOME_UTILIZADOR"] = $nome;
                    
                    
                    // encaminhar com timer 3 segundos
                    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // past date to encourage expiring immediately
                    //header("Refresh: 3; URL=index.php");
                } else{
                    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($_conn);
                    echo "STATUS ADMIN (alterar definições): " . mysqli_error($_conn);
                }
                mysqli_stmt_close($stmt2);
                    
                } else{
                    //echo "ERROR: Could not prepare query: $sql. " . mysqli_error($_conn);
                    echo "STATUS ADMIN (alterar definições): " . mysqli_error($_conn);
                }
                mysqli_stmt_close($stmt);
                
        }
            
            
}

if ( isset($_POST["apagar-post"])  ) {
    
    // fazer update à tabela de USERS para atualizar o estado e limpar o token
    $sql= apagarPost();
    
    if ( $stmt = mysqli_prepare($_conn, $sql) ) {
        $idPost = $_POST["idPost"];
        mysqli_stmt_bind_param($stmt, "i", $idPost);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        
        
    } else {

        $stmt->close();
        // falhou a atualização
        
        echo "STATUS ADMIN (apagar post): " . mysqli_error($_conn);
    }
    
    
    
    
}

 
?>
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#pic').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    
</script>

<!DOCTYPE html>
<html>
<head>
<title>Editar conta</title>
<?php include_once "style.html";?>
<body>
<?php include_once "navbar.php";?>

<div class="w3-theme w3-padding-64 w3-padding-large w3-center">
      <h1>Alterar Perfil</h1>
      <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
      <div class="w3-section">
      <h2>Dados</h2>
      <form form action="#" method="POST" enctype="multipart/form-data">
      <p>Por uma questão de segurança, para alterar as suas definições de conta deverá digitar a sua senha. No final, não se esqueça de gravar as alterações.<br>Se apenas pretende alterar a sua senha use a opção <a href="userRecuperarSenha.php">recuperar senha</a></p>
      <p><?php echo $msgTemporaria;?></p>
        <div class="w3-section">
        <input type="file" name="file" id="file" style="opacity:0;" <?php if(!isset($_FILES["file"])){ ?>onchange="readURL(this);" <?php }?>/>
        <p><?php echo $mensagemErroFoto;?></p>
        <label for="file">
            <img id="pic" name="pic" src="<?php echo $pic;?>" class="w3-center w3-image w3-circle w3-hover-opacity">
        </label>
        </div>
        <div class="w3-section">
          <label>Nome</label><p><?php echo $mensagemErroNome;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;"type="text" id="nome" name="nome" value="<?php echo $nome;?>" required placeholder="Nome">
        </div>
        <div class="w3-section">
          <label>Apelido</label><p><?php echo $mensagemErroApelido;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="text" id="apelido" name="apelido" value="<?php echo $apelido;?>" placeholder="Apelido">
        </div>
        <div class="w3-section">
          <label>Telemovel</label><p><?php echo $mensagemErroTelemovel;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" maxlength="12" style="width:100%;"type="text" id="telemovel" name="telemovel" size="12" value="<?php echo $telemovel;?>" placeholder="Telemovel">
        </div>
        <div class="w3-section">
        <label>Visibilidade</label>
        <select class="w3-select w3-border w3-round-large" name="visibilidade">
               <option value="Sim" <?php if ($visivel == 2 ) { echo " selected"; } ?>>Privado</option>
               <option value="Não" <?php if ($visivel == 1 ) { echo " selected"; } ?>>Publico</option>
       </select>
        </div>
        <div class="w3-section">
        <label>Pretendo receber mensagens de marketing</label>
         <select class="w3-select w3-border w3-round-large" name="receberMensagens">
                <option value="Sim" <?php if ($receberMsgs == 2 ) { echo " selected"; } ?>>Sim</option>
                <option value="Não" <?php if ($receberMsgs == 1 ) { echo " selected"; } ?>>Não</option>
        </select>
        </div>
        <div class="w3-section">
          <label>Senha</label><p><?php echo $mensagemErroSenha;?></p>
          <input class="w3-input w3-theme-l5 w3-border w3-round-large" style="width:100%;" type="password" id="senha" name="senha" value="<?php echo $senha;?>" required placeholder="Senha">
        </div>
        <input class="w3-btn" type="submit" value="Gravar Alterações" id="botao-gravar-alteracoes" name="botao-gravar-alteracoes">
        <button form="cancelar" name="botao-cancelar-minha-conta" type="submit" class="w3-btn">Cancelar Conta</button>
      </form>
      <form id="cancelar" action="#" method="POST">
      </form>
</div>

      <?php
    $username = "";
    $post = "";
    $dataHora = "";
    $sql = verPostsUtilizador($codigo);
    $resultadoTabela = mysqli_query($_conn, $sql);           
     if (mysqli_num_rows($resultadoTabela) > 0) {
          $ctd = 0;
          echo"<hr><div class='w3-section w3-padding-large'><h2>Posts</h2>";
          while($rowTabela = mysqli_fetch_assoc($resultadoTabela)) {
              $ctd=$ctd+1;
              $username = $rowTabela["username"];
              $post = $rowTabela["post"];
              $post = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $post);
              $post = wordwrap($post, 10, "\n", true);
              $dataHora = $rowTabela["data_hora"];
           ?>
           <div class="w3-padding-16">
           <div class="w3-section w3-panel w3-round-large w3-theme-l2 w3-card-4 w3-padding">
           <div class="w3-quarter w3-center">
           <p><?php echo $username?></p><p><?php echo $dataHora?></p>
           </div>
           <div class="w3-half w3-center">
           <p><?php echo $post?></p>
           </div>
           <div class="w3-quarter w3-center">
           <form action="#" method="POST">
               <button type="submit" name="apagar-post" class="w3-button"><i class="material-icons w3-xxlarge">delete</i></button>
               <input id="idPost" name="idPost" type="hidden" value="<?php echo $rowTabela["id"]; ?>">
           </form>
           </div>
           </div>
          </div>
           <?php
       }
       echo"</div>";
      } mysqli_free_result($resultadoTabela);
       ?>
       <form id="voltar" action="#" method="POST">
       <button form="voltar" name="botao-voltar" type="submit" class="w3-btn w3-xlarge">Voltar</button>
      </form>
    </div>
</div>
<?php include_once "footer.php";?>
</body>
</html>