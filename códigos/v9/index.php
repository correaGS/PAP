<?php  
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
include_once  'conexaobasedados.php'; 
include_once "comandosbasedados.php";
?>

<!DOCTYPE html>
<html lang="en">
<title>Projeto</title>
<?php include_once "style.html";?>
<body>  

<?php include_once "navbar.php";?> 
<?php include_once "content.php";?> 
<?php include_once "footer.php";?> 

</body>
</html>