<?php 
session_start();
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign out</title>
</head>
<body>
    <h2>Vous êtes bien déconnecté</h2>
</body>
</html>
<?php 
//header("location : index.php");
?>