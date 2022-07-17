<?php
	session_start();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    error_reporting(E_ALL);
    include_once  'conexaobasedados.php'; 
    include_once 'comandosbasedados.php';


    $id_categoria = $_POST["id_categoria"];


    if(!empty($id_categoria)){
        
        $subcategoria_sql = get_subcategoria($id_categoria);
        $resultado_subcategorias = mysqli_query($_conn, $subcategoria_sql);
        if (mysqli_num_rows($resultado_subcategorias) > 0) {

            ?>
            <label><h4>Sub Categoria</h4></label>
            <select class="w3-select w3-border w3-round-large w3-text-black w3-margin-bottom" name="sub_categoria" id="sub_categoria">
                <option value="" disabled selected>Selecione uma sub-categoria...</option>
            <?php

            while ($row_subcategoria = mysqli_fetch_array($resultado_subcategorias)){

                ?><option value="<?php echo $row_subcategoria["id"]?>"><?php echo $row_subcategoria["descricao"]?></option><?php

            }
        }
          mysqli_free_result($resultado_subcategorias);

        ?>
        </select>
        <?php

    }else{echo "";}
?>

        