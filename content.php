<!-- Header -->
<header class="w3-container w3-theme w3-center" style="padding:128px 16px">
  <h1 class="w3-margin w3-jumbo">Sistema X</h1>
</header>

<!-- First Grid -->
<div class="w3-row-padding w3-padding-64 w3-container w3-theme-l5">
  <div class="w3-content">
    <div class="w3-twothird">
      <h1>Conteudo Publico</h1>

      <p class="w3-text-grey">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint
        occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
        laboris nisi ut aliquip ex ea commodo consequat.</p>
    </div>

    <div class="w3-third w3-center">
      <i class="fa fa-anchor w3-padding-64 w3-text-theme"></i>
    </div>
  </div>
</div>

<!-- Second Grid -->
<?php if (isset($_SESSION["UTILIZADOR"]) ) { ?>  
<div class="w3-row-padding w3-theme-l5 w3-padding-64 w3-container">
  <div id ="posts" class="w3-content">
    <h1  class="w3-center">Conteudo Privado</h1>
    <br>

<?php

if (isset($_GET['pagenum'])) {
  $pagenum = $_GET['pagenum'];
} else {
  $pagenum = 1;
}
$pagePosts = 4;
$offset = ($pagenum - 1) * $pagePosts;


$username = "";
$post = "";
$dataHora = "";
$sql = countPosts();
   $allPosts = mysqli_query($_conn, $sql);           
   if (mysqli_num_rows($allPosts) > 0) {
      $total_rows = mysqli_fetch_array($allPosts)[0];
      $total_pages = ceil($total_rows / $pagePosts);
      mysqli_free_result($allPosts);

      $sql = verPosts($offset, $pagePosts);
      $resultadoTabela = mysqli_query($_conn, $sql);   
      if (mysqli_num_rows($resultadoTabela) > 0) {
       $ctd = 0;
       while($rowTabela = mysqli_fetch_array($resultadoTabela)) {
           $ctd=$ctd+1;
           $pic = $rowTabela["foto"];
           $username = $rowTabela["username"];
           $post = $rowTabela["post"];
           $post = str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $post);
           $post = wordwrap($post, 120);
           $dataHora = $rowTabela["data_hora"];
           ?>
           <div class="w3-padding-16">
           <div class="w3-section w3-panel w3-round-large w3-theme-l2 w3-card-4 w3-padding">
           <div class="w3-quarter w3-center">
           <p><img src=<?php echo $pic;?> class="w3-center w3-image w3-circle" style="width:180px;"></p>
           <p><?php echo $username;?></p><p><?php echo $dataHora;?></p>
           </div>
           <div class="w3-threequarter w3-center">
           <p><?php echo $post;?></p>
           </div>
           </div>
           </div>
           <?php
       }
      }mysqli_free_result($resultadoTabela);
     
    }
       ?>
       <div class="w3-center">
          <ul class="pagination">
        <li><a href="?pagenum=1#posts">First</a></li>
        <li class="<?php if($pagenum <= 1){ echo 'disabled'; } ?>">
            <a href="<?php if($pagenum <= 1){ echo '#posts'; } else { echo '?pagenum='.($pagenum - 1)."#posts"; } ?>">Prev</a>
        </li>
        <li class="<?php if($pagenum >= $total_pages){ echo 'disabled'; } ?>">
            <a href="<?php if($pagenum >= $total_pages){ echo '#posts'; } else { echo "?pagenum=".($pagenum + 1)."#posts"; } ?>">Next</a>
        </li>
        <li><a href="?pagenum=<?php echo $total_pages; ?>#posts">Last</a></li>
    </ul>
  </div>
      </div>
    </div>
    <div class="w3-row-padding w3-theme-l5 w3-padding-32 w3-container">
  <div class="w3-content w3-center"> 
  <a href="perfis.php" class=" w3-button w3-theme w3-padding-large w3-text-grey">Ver Perfis Públicos</a>
  <a href="posts.php" class=" w3-button w3-theme w3-padding-large w3-text-grey">Criar Posts</a>
  
  </div>
</div>

<?php }  ?>