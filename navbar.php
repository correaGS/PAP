<?php if (isset($_SESSION["UTILIZADOR"]) ) { ?> 

<!-- Navbar -->    
<div class="w3-top">
  <div class="w3-bar w3-theme-d1 w3-card w3-left-align w3-large">
    <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large  w3-large w3-theme-d1" href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="material-icons w3-padding-small">menu</i></a>
    <a href="index.php" class="w3-bar-item w3-button w3-padding-large w3-theme-l4"><i class="material-icons w3-padding-small">home</i></a>
    <a href="userSair.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1"><i class="material-icons w3-padding-small">logout</i></a>
    <a href="userEditarConta.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1 w3-right">
    <b><?php echo $_SESSION["NOME_UTILIZADOR"];?></b><i class="material-icons w3-padding-small">settings_applications</i></a>
    <?php if(isset($_SESSION["NIVEL_UTILIZADOR"])){
        if($_SESSION["NIVEL_UTILIZADOR"]==3) { ?>
        <a href="userGerirUtilizadores.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1"><i class="material-icons w3-padding-small">manage_accounts</i></a>
    <?php }} ?>
     
  </div>

<!-- Navbar on small screens -->
<div id="navDemo" class="w3-bar-block w3-theme-l4 w3-hide w3-hide-large w3-hide-medium w3-large">
    <a href="userSair.php" class="w3-bar-item w3-button w3-padding-large"><i class="material-icons w3-padding-small">logout</i></a>
    <?php if(isset($_SESSION["NIVEL_UTILIZADOR"])){
        if($_SESSION["NIVEL_UTILIZADOR"]==3) { ?>
        <a href="userGerirUtilizadores.php" class="w3-bar-item w3-button w3-padding-large"><i class="material-icons w3-padding-small">manage_accounts</i></a>
    <?php }} ?>
    <a href="userEditarConta.php" class="w3-bar-item w3-button w3-padding-large"><b><?php echo $_SESSION["NOME_UTILIZADOR"];?></b><i class="material-icons w3-padding-small">settings_applications</i></a>
</div>
</div>

  <?php } else { ?>
    
<!-- Navbar -->    
<div class="w3-top">
        <div class="w3-bar w3-theme-d1 w3-card w3-left-align w3-large">
          <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-large w3-theme-d1" href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="material-icons w3-padding-small">menu</i></a>
          <a href="index.php" class="w3-bar-item w3-button w3-padding-large w3-theme-l4"><i class="material-icons w3-padding-small">home</i></a>
          <a href="userEntrar.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1"><i class="material-icons w3-padding-small">login</i></a>
          <a href="userCriarConta.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1">Criar Conta</a>
        </div>
      
      <!-- Navbar on small screens -->
      <div id="navDemo" class="w3-bar-block w3-theme-l4 w3-hide w3-hide-large w3-hide-medium w3-large">
          <a href="userEntrar.php" class="w3-bar-item w3-button w3-padding-large"><i class="material-icons">login</i></a>
          <a href="userCriarConta.php " class="w3-bar-item w3-button w3-padding-large">Criar Conta</a>
      </div>
      </div>

<?php } ?> 
<script>
// Used to toggle the menu on small screens when clicking on the menu button
function myFunction() {
  var x = document.getElementById("navDemo");
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
  } else { 
    x.className = x.className.replace(" w3-show", "");
  }
}
</script>