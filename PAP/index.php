<?php  
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once "comandosbasedados.php";
?>

<!DOCTYPE html>
<html lang="pt">
<title>Home</title>
<?php include_once "style.html";?>
<body>  
<div class = "flex-wrapper w3-auto w3-responsive">
<?php include_once "navbar.php";?> 
<?php include_once "content.php";?> 
<?php include_once "footer.php";?> 
</div>

</body>
</html>