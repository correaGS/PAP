<?php

    session_start();
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    error_reporting(E_ALL);
    include_once  'conexaobasedados.php'; 
    include_once 'comandosbasedados.php';

    if(isset($_POST["image"])){

        $data = $_POST['image'];

        $user = $_SESSION["UTILIZADOR"];

        $image_array_1 = explode(";", $data);

        $image_array_2 = explode(",", $image_array_1[1]);

        $data = base64_decode($image_array_2[1]);

        $image_name = "imagens/" . $user . "/" . time() . ".webp";

        file_put_contents($image_name, $data);

        $sql = update_profile_picture();
        $stmt = $_conn->prepare($sql);
        $stmt->bind_param('ss', $image_name, $user);
        $stmt->execute();

        echo $image_name;

    }

?>