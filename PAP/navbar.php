<?php if (isset($_SESSION["UTILIZADOR"]) ) { ?> 

<!-- Navbar -->    
<div class="w3-top">
  <div class="w3-bar w3-theme-d1 w3-card w3-left-align w3-large">
    <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large  w3-large w3-theme-d1" href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa-solid fa-bars w3-padding-small"></i></a>
    <a href="index.php" class="w3-bar-item w3-button w3-padding-large w3-theme-l4"><i class="fa-solid fa-house w3-padding-small"></i></a>
    <div class="w3-bar-item w3-hide-small w3-center" style="padding: 10px 8px;"> <div class="w3-cell w3-cell-middle"> <div class="translate" id="google_translate_element"></div> </div> <div class="w3-cell w3-cell-middle"> <button class="btn w3-theme-d1 w3-hover-red btn-sm" onclick="MyReset()"><i class="fa-solid fa-xmark"></i></button> </div> </div>
    <a href="userSair.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1"><i class="fa-solid fa-right-from-bracket w3-padding-small"></i></a>
    <a href="userEditarConta.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1 w3-right">
    <div><b><?php echo $_SESSION["NOME_UTILIZADOR"];?></b><i class="fa-solid fa-user w3-padding-small"></i></div></a>
    <?php if(isset($_SESSION["NIVEL_UTILIZADOR"])){
        if($_SESSION["NIVEL_UTILIZADOR"]==3) { ?>
        <a href="userGerirUtilizadores.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1"><i class="fa-solid fa-user-gear w3-padding-small"></i></a>
        <a href="userGerirReportes.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1"><i class="fa-solid fa-shield-halved w3-padding-small"></i></a>
    <?php }} ?>
     
  </div>

<!-- Navbar on small screens -->
<div id="navDemo" class="w3-bar-block w3-theme-l4 w3-hide w3-hide-large w3-hide-medium w3-large">
    <a href="userSair.php" class="w3-bar-item w3-button w3-padding-large"><i class="fa-solid fa-right-from-bracket w3-padding-small"></i></a>
    <?php if(isset($_SESSION["NIVEL_UTILIZADOR"])){
        if($_SESSION["NIVEL_UTILIZADOR"]==3) { ?>
        <a href="userGerirUtilizadores.php" class="w3-bar-item w3-button w3-padding-large"><i class="fa-solid fa-user-gear w3-padding-small"></i></a>
        <a href="userGerirReportes.php" class="w3-bar-item w3-button w3-padding-large"><i class="fa-solid fa-shield-halved w3-padding-small"></i></a>
    <?php }} ?>
    <a href="userEditarConta.php" class="w3-bar-item w3-button w3-padding-large"><b><?php echo $_SESSION["NOME_UTILIZADOR"];?></b><i class="fa-solid fa-user w3-padding-small"></i></a>
</div>
</div>

  <?php } else { ?>
    
<!-- Navbar -->    
<div class="w3-top">
        <div class="w3-bar w3-theme-d1 w3-card w3-left-align w3-large">
          <a class="w3-bar-item w3-button w3-hide-medium w3-hide-large w3-right w3-padding-large w3-large w3-theme-d1" href="javascript:void(0);" onclick="myFunction()" title="Toggle Navigation Menu"><i class="fa-solid fa-bars w3-padding-small"></i></a>
          <a href="index.php" class="w3-bar-item w3-button w3-padding-large w3-theme-l4"><i class="fa-solid fa-house w3-padding-small"></i></a>
          <div class="w3-bar-item w3-hide-small w3-center" style="padding: 10px 8px;"> <div class="w3-cell w3-cell-middle"> <div class="translate" id="google_translate_element"></div> </div> <div class="w3-cell w3-cell-middle"> <button class="btn w3-theme-d1 w3-hover-red btn-sm" onclick="MyReset()"><i class="fa-solid fa-xmark"></i></button> </div> </div>
          <a href="userEntrar.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1"><i class="fa-solid fa-right-to-bracket w3-padding-small"></i></a>
          <a href="userCriarConta.php" class="w3-bar-item w3-button w3-hide-small w3-padding-large w3-theme-d1">Criar Conta</a>
        </div>
      
      <!-- Navbar on small screens -->
      <div id="navDemo" class="w3-bar-block w3-theme-l4 w3-hide w3-hide-large w3-hide-medium w3-large">
          <a href="userEntrar.php" class="w3-bar-item w3-button w3-padding-large"><i class="fa-solid fa-right-to-bracket"></i></a>
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

<script type="text/javascript">
      function googleTranslateElementInit() {
        new google.translate.TranslateElement({pageLanguage: 'pt', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
      }
      function MyReset() {
        
        //jQuery('#\\:2\\.finishSection').contents().find('#\\:2\\.restore').click();
        //var iframe = document.getElementById("\\:2\\.container");
        //var elmnt = iframe.contentWindow.document.getElementsByTagName("\\:2\\.restore")[0];
        //elmnt.click();

        var iframe = $('#\\:2\\.container').contents();
        iframe.find("#\\:2\\.restore").click();
      }
      
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>


