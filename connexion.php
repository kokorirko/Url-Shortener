<?php 
session_start();
$bdd = new PDO('mysql:host=127.0.0.1;dbname=URLmembers','root', '');
if(isset($_POST['submit'])) {
    $mail = htmlspecialchars($_POST['mail']);
    $pass = sha1($_POST['pass']);
    if(!empty($mail) AND !empty($pass)) {
       $stmt = $bdd->prepare("SELECT * FROM URLmembers WHERE mail = ? AND password = ?");
       $stmt->execute(array($mail, $pass));
       $userexist = $stmt->rowCount();
       if($userexist == 1) {
          $userinfo = $stmt->fetch();
          $_SESSION['ID'] = $userinfo['ID'];
          $_SESSION['username'] = $userinfo['username'];
          $_SESSION['mail'] = $userinfo['mail'];
          header("Location: profil.php?id=".$_SESSION['ID']);
          echo $_SESSION['mail'] . ' ' . $_SESSION['ID'] . ' ' . $_SESSION['username'];

        
       } else {
          $erreur = "Wrong mail or password";
       }
    } else {
       $erreur = "All field must be filled !";
    }
 }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <form method="POST" action="">
        <input type="mail" name="mail" placeholder="your mail"> <br>
        <input type="password" name="pass" placeholder="your password">
        <input type="submit" name="submit" value="connexion">
    </form>
    <?php
         if(isset($erreur)) {
            echo '<font color="red">'.$erreur."</font>";
         }
         ?>
</body>
</html>