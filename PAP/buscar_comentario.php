<?php
	session_start();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    error_reporting(E_ALL);
    include_once  'conexaobasedados.php'; 
    include_once 'comandosbasedados.php';

    $id_post = $_POST["id_post"];


        $sql = ver_comentarios();
        $stmt = $_conn -> prepare($sql);
        $stmt -> bind_param('i', $id_post);
        $stmt -> execute();
        $resultado = $stmt -> get_result();
        $dados = $resultado -> fetch_all(MYSQLI_ASSOC);
        $output = "";

        foreach($dados as $row){

            $votos = countVotes_comentario($row["id"]);
                $resultadoVotos = mysqli_query($_conn, $votos);
                if (mysqli_num_rows($resultadoVotos) > 0){
                  while($rowTabelaVotos = mysqli_fetch_array($resultadoVotos)) {
                  $total_votos = $rowTabelaVotos["likes"];
                  }
                }
                mysqli_free_result($resultadoVotos);

                $ja_votou = ja_votou_comentario();
                $stmt_ja_votou = $_conn->prepare($ja_votou);
                $stmt_ja_votou ->bind_param('is', $row["id"], $_SESSION['UTILIZADOR']);
                $stmt_ja_votou ->execute();

                $resultado_ja_votou = $stmt_ja_votou->get_result();
                
                if ($resultado_ja_votou->num_rows > 0) {
                  while ($row_ja_votou = $resultado_ja_votou->fetch_assoc()) {
                      if ($row_ja_votou['vote']==1){
                        $voto="like";
                      } else if($row_ja_votou['vote']==-1){
                        $voto="dislike";
                      }
                  }
                } else {
                   $voto="nao";
                }
                $stmt_ja_votou->free_result();
                $stmt_ja_votou->close();

                if($row["visibilidade"] == 8){
                    $output .= "
                    <div class='panel panel-default'>
                        <div class='panel-heading' align='left'>
                                <form action='reportar.php' method='POST'>
                                <b> [DELETADO] • </b><i>". $row["data_hora"] ." </i>
                                        <input type='hidden' name='id' value='".$row["id"]."'>
                                        <button  style='margin:5px;' type='submit' name='report' value='comentario' class='btn w3-theme w3-hover-purple btn-sm' disabled><i class='fa-solid fa-flag'></i></button>
                                    </form>
                        </div>
                        <div class='panel-body justificado'>[DELETADO]</div>
                        <div class='panel-footer'>
                            <div class='row'>
                            <div class='col-sm-6' align='left'>
                                <input type='button' voto='". $voto ."' id='like_button_".$row["id"]."' onclick='like(". $row["id"] .")' value='Like' disabled></input>
                                <b id='total_votos_comentario_".$row["id"]."'>". $total_votos ."</b>
                                <input type='button' voto='". $voto ."' id='dislike_button_".$row["id"]."' onclick='dislike(". $row["id"] .")' value='dislike' disabled></input>
                            </div>
                                <div class='col-sm-6' align='right'>
                                    <button type='button' class='btn btn-default reply' id='". $row["id"] ."' user_reply='". $row["user"] ."' disabled>Responder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=' reply-box-1 reply-form' id='reply_form_".$row["id"]."'></div>
                    ";

                }else if ($row["visibilidade"] == 1 && !empty($row["user"])){
                    $output .= "
                    <div class='panel panel-default'>
                        <div class='panel-heading' align='left'>
                                
                                <form id='report_form' action='reportar.php' method='POST'>
                                    <b><img src='".$row["foto"]."'style=' margin-right: 8px; width:45px; height: 45px; min-width:45px; nin-height: 45px; max-width:45px; max-height: 45px;' class='w3-image img-circle w3-center'>". $row["user"] ." • </b><i>". $row["data_hora"] ." </i>
                                    <input type='hidden' name='id' value='".$row["id"]."'>
                                    <button  style='margin:5px;' type='submit' name='report' value='comentario' class='btn w3-theme w3-hover-purple btn-sm'><i class='fa-solid fa-flag'></i></button>
                                </form>
                            </div>
                        <div class='panel-body justificado'>".  str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $row["text"]) ."</div>
                        <div class='panel-footer' >
                            <div class='row'>
                                <div class='col-sm-6' align='left'>
                                    <input type='button' voto='". $voto ."' id='like_button_".$row["id"]."' onclick='like(". $row["id"] .")' value='Like'></input>
                                    <b id='total_votos_comentario_".$row["id"]."'>". $total_votos ."</b>
                                    <input type='button' voto='". $voto ."' id='dislike_button_".$row["id"]."' onclick='dislike(". $row["id"] .")' value='dislike'></input>
                                </div>
                                <div class='col-sm-6' align='right'>
                                    <button type='button' class='btn btn-default reply' id='". $row["id"] ."' user_reply='". $row["user"] ."'>Responder</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='reply-box-1 reply-form' id='reply_form_".$row["id"]."'></div>
                    ";
                }

           

            $output .=get_resposta_comentario($_conn, $row["id"], $id_post);

        }

        echo $output;

        function get_resposta_comentario($_conn, $parent_id = 0, $id_post, $margin_left = 0){
            $output="";
            $sql = ver_respostas();
            $stmt = $_conn->prepare($sql);
            $stmt->bind_param('ii', $id_post, $parent_id);
            $stmt->execute();
            $resultado = $stmt -> get_result();
            $dados = $resultado -> fetch_all(MYSQLI_ASSOC);
            $count = count($dados);
            $stmt -> close();
            if($parent_id == 0){

                $margin_left = 0;
                $reply_class = "";
            } else {
                if($margin_left <= 96){
                    $margin_left = $margin_left + 48;
                }
                if($margin_left == 48){ $reply_class = "reply-box-1"; $reply_form_class = "reply-box-2"; } 
                else if ($margin_left == 96){ $reply_class = "reply-box-2"; $reply_form_class = "reply-box-3";} 
                else if ($margin_left == 144){ $reply_class = "reply-box-3"; $reply_form_class = "reply-box-3";}
            }

            if($count > 0){

                foreach($dados as $row){

                    $votos = countVotes_comentario($row["id"]);
                    $resultadoVotos = mysqli_query($_conn, $votos);
                    if (mysqli_num_rows($resultadoVotos) > 0){
                      while($rowTabelaVotos = mysqli_fetch_array($resultadoVotos)) {
                      $total_votos = $rowTabelaVotos["likes"];
                      }
                    }
                    mysqli_free_result($resultadoVotos);
    
                    $ja_votou = ja_votou_comentario();
                    $stmt_ja_votou = $_conn->prepare($ja_votou);
                    $stmt_ja_votou ->bind_param('is', $row["id"], $_SESSION['UTILIZADOR']);
                    $stmt_ja_votou ->execute();
    
                    $resultado_ja_votou = $stmt_ja_votou->get_result();
                    
                    if ($resultado_ja_votou->num_rows > 0) {
                      while ($row_ja_votou = $resultado_ja_votou->fetch_assoc()) {
                          if ($row_ja_votou['vote'] == 1){
                            $voto="like";
                          } else if($row_ja_votou['vote'] == -1){
                            $voto="dislike";
                          }
                      }
                    } else {
                       $voto="nao";
                    }
                    
                    $stmt_ja_votou->free_result();
                    $stmt_ja_votou->close();

                    if(empty($row["user_reply"])){
                        $user_reply = "[DELETADO]";
                    }else{$user_reply = $row["user_reply"];}

                    if($row["visibilidade"] == 1 && !empty($row["user"])){
                        $output .= "
                        <div class='panel panel-default ". $reply_class ."'>
                        <div class='panel-heading' align='left'>
                                    <form id='report_form' action='reportar.php' method='POST'>
                                    <b><img src='".$row["foto"]."'style=' margin-right: 8px; width:45px; height: 45px; min-width:45px; nin-height: 45px; max-width:45px; max-height: 45px;' class='w3-image img-circle w3-center'>". $row["user"] ." em resposta a ". $user_reply ." • </b><i>". $row["data_hora"] ." </i>
                                        <input type='hidden' name='id' value='".$row["id"]."'>
                                        <button  style='margin:5px;' type='submit' name='report' value='comentario' class='btn w3-theme w3-hover-purple btn-sm'><i class='fa-solid fa-flag'></i></button>
                                    </form>
                        </div>
                            <div class='panel-body justificado'>". str_replace(array('\r\n', '\n\r', '\n', '\r'), '<br>', $row["text"]) ."</div>
                            <div class='panel-footer'>
                                <div class='row'>
                                    <div class='col-sm-6' align='left'>
                                        <input type='button' voto='". $voto ."' id='like_button_".$row["id"]."' onclick='like(". $row["id"] .")' value='Like'></input>
                                        <b id='total_votos_comentario_".$row["id"]."'>". $total_votos ."</b>
                                        <input type='button' voto='". $voto ."' id='dislike_button_".$row["id"]."' onclick='dislike(". $row["id"] .")' value='dislike'></input>
                                    </div>
                                    <div class='col-sm-6' align='right'>
                                        <button type='button' class='btn btn-default reply' id='". $row["id"] ."' user_reply='". $row["user"] ."'>Responder</button>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <div class='reply-form ". $reply_form_class ."' id='reply_form_".$row["id"]."'></div>
                        ";
                    }
                    else if($row["visibilidade"] == 8){
                        $output .= "
                        <div class='panel panel-default ". $reply_class ."'>
                        <div class='panel-heading' align='left'>
                                    
                                    <form action='reportar.php' method='POST'>
                                    <b> [DELETADO] em resposta a ". $user_reply ." • </b><i>". $row["data_hora"] ." </i>
                                        <input type='hidden' name='id' value='".$row["id"]."'>
                                        <button  style='margin:5px;' type='submit' name='report' value='comentario' class='btn w3-theme w3-hover-purple btn-sm' disabled><i class='fa-solid fa-flag'></i></button>
                                    </form>
                        </div>
                            <div class='panel-body justificado'> [DELETADO] </div>
                            <div class='panel-footer'>
                                <div class='row'>
                                    <div class='col-sm-6' align='left'>
                                        <input type='button' voto='". $voto ."' id='like_button_".$row["id"]."' onclick='like(". $row["id"] .")' value='Like' disabled></input>
                                        <b id='total_votos_comentario_".$row["id"]."'>". $total_votos ."</b>
                                        <input type='button' voto='". $voto ."' id='dislike_button_".$row["id"]."' onclick='dislike(". $row["id"] .")' value='dislike' disabled></input>
                                    </div> 
                                    <div class='col-sm-6' align='right'>
                                        <button type='button' class='btn btn-default reply' id='". $row["id"] ."' user_reply='". $row["user"] ."' disabled>Responder</button>
                                   </div> 
                                </div>
                            </div>
                        </div>
                        <div class='reply-form ". $reply_form_class ."' id='reply_form_".$row["id"]."'></div>
                        ";
                    }

                    
                    $output .= get_resposta_comentario($_conn, $row["id"], $id_post, $margin_left);

                }

            }

            return $output;

        }
?>