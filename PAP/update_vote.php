<?php 
session_start();
include_once  'conexaobasedados.php'; 
include_once 'comandosbasedados.php';

$action = $_POST['action'];
$id = $_POST['id'];
$user = $_SESSION['UTILIZADOR'];

if($action == "like"){

    $sql = like_post();
    if($stmt = mysqli_prepare($_conn, $sql)){
        $stmt->bind_param('is', $id, $user);
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();

    }else{    
    echo "STATUS ADMIN (inserir voto): " . mysqli_error($_conn);
    }

} else if($action == "dislike"){
    
    $sql = dislike_post();
    if($stmt = mysqli_prepare($_conn, $sql)){
        $stmt->bind_param('is', $id, $user);
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();

    }else{    
    echo "STATUS ADMIN (inserir voto): " . mysqli_error($_conn);
    }

} else if($action == "remove"){
    
    $sql = remove_like_post();
    if($stmt = mysqli_prepare($_conn, $sql)){
        $stmt->bind_param('is', $id, $user);
        $stmt->execute();
        $stmt->free_result();
        $stmt->close();

    }else{    
    echo "STATUS ADMIN (remover voto): " . mysqli_error($_conn);
    }

}
?>