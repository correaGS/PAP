<?php 

  session_start();
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  error_reporting(E_ALL);
  // estabelecer conexão à base de dados

  include_once  'conexaobasedados.php'; 
  include_once 'comandosbasedados.php';

  //$mensagemErroPost = "";
  $post = "";
  $titulo = "";
  $codigo = "";
  $mensagem = "";
  $erro = "";


  if ( !isset($_SESSION["UTILIZADOR"])) {
    ?>
    <script type="text/javascript">
    alert("É preciso estar logado para fazer um reporte");
    window.location = "userEntrar.php";
    </script>
    <?php
  }

  if ( !isset($_POST["report"]) || !isset($_POST["id"]) ) {
    ?>
    <script type="text/javascript">
        alert("Dados de reporte inválidos");
        
    </script>
    <?php
  }else{
    $tipo = $_POST["report"];
    $id = $_POST["id"];
  }

  $motivo_sql = get_motivo();
  $resultado_motivo = mysqli_query($_conn, $motivo_sql);

?>
<!DOCTYPE html>
<html>
<title>Reportar</title>
<?php include_once "style.html";?>
<body>
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?>
<div class="content w3-theme w3-padding-64 w3-padding-large w3-center w3-container">
  <div class="center">
  <h1>Reportar</h1>
  <p><?php echo $mensagem;?></p>
  <div class="w3-container w3-padding-32 w3-theme-l5 w3-round-large">
    <form method="POST" id="form_reportar">
      <div class="w3-section">
      <div id="select_categoria">
        <label><h4>Motivo</h4></label>
         <select class="w3-select w3-border w3-round-large w3-margin-bottom" name="motivo" id="motivo" required>
          <option value=""  disabled selected>Selecione o motivo...</option>
            <?php 
              while ($row_motivo = mysqli_fetch_array($resultado_motivo)){
                ?><option value="<?php echo $row_motivo["id"]?>"><?php echo $row_motivo["descricao"]?></option><?php
              }mysqli_free_result($resultado_motivo);
            ?>
        </select>
      </div>
      <div id="outro_motivo"></div>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <input type="hidden" name="report" value="<?php echo $tipo; ?>">
        <?php echo $erro;?>
      </div>
      <button name="botao-reportar" type="submit" class="w3-btn">Reportar</button>
      <button name="botao-voltar" type="button" id="botao-voltar" class="w3-btn">Voltar</button>
    </form>
  </div>
  </div>
</div>

<?php include_once "footer.php";?>
</div>
</body>
</html>

<script>
  $("#botao-voltar").click(function (){
    if ('referrer' in document) {
        window.location = document.referrer;
    } else {
        window.history.back();
    }
});

  $(document).ready(function() {

    $('#motivo').on('change', function() {

      var id_motivo = this.value;
        
      if(id_motivo == 7){
        $("#outro_motivo").html("\
        <label><h4>Outro Motivo</h4></label>\
        <textarea class='w3-input w3-margin-bottom w3-border w3-round-large w3-theme-l5' style='width:100%; overflow-x: hidden;overflow-y: scroll;' rows='4' type='text' maxlength='500' name='outro_motivo' required placeholder='Digite o motivo...'></textarea>\
        ")
      } else {$("#outro_motivo").html("")}

    });

    $('#form_reportar').on('submit', function(event){
        event.preventDefault();
        var dados_formulario = $(this).serialize();
        $.ajax({
          url:"adicionar_reporte.php",
          method:"POST",
          data: dados_formulario,
          dataType:"JSON",
          success:function(dados){
            if(dados.erro != ''){
                alert("Erro ao submeter report.");
                if ('referrer' in document) {
                    window.location = document.referrer;
                } else {
                    window.history.back();
                }
                
            }else{
                alert("Agradecemos o reporte!\r\nAssim que possível o reporte será verificado.");
                if ('referrer' in document) {
                    window.location = document.referrer;
                } else {
                    window.history.back();
                }
            }
          }
        })
    });

  });
</script>
