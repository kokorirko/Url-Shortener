<?php 

    session_start();
    $bdd = new PDO('mysql:host=127.0.0.1;dbname=URLmembers','root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if(isset($_GET['id']) AND $_GET['id'] > 0){
        $getid = intval($_GET['id']);
        $stmt = $bdd->prepare('SELECT * FROM URLmembers WHERE id = ? ');
        $stmt->execute(array($getid));
        $userinfo = $stmt->fetch();
    }
    $baseUrl = "localhost/URL/";
    $msg = ' ';
    
    function shortenUrl($url){
        if (filter_var($url, FILTER_VALIDATE_URL)){
            $randStr = substr(str_shuffle(md5(rand())),0,6);
            $oldfile = file_get_contents('url_list.php') . "\n";
            $newfile = '$list[\''.$randStr.'\']=\''.$url.'\';';
            // file_put_contents('url_list.php', $oldfile.$newfile);
            return $randStr;
        } else{
            return false;
        }
    }
    if(isset($_POST['url'])) { 
        $check = shortenUrl($_POST['url']);
        setcookie("UrlCookie", $_POST['url'], time() + (60),"/"); // this cookie allows us to delete the temporary file, later
        $ID = $getid;
        $original = $_POST['url'];
        $shorten = $check . '.php';
        $linkID = rand(10000,999999) + rand(10,150) - rand(150,1000); 
        $active = true;
        $views = 0;
        $date = date("Y-m-d");
        $stmm = $bdd->prepare("INSERT INTO urllinks (ID, Original, Shorten, LinkID, Active, Views, Date)VALUES(?,?,?,?,?,?,?)");
        $stmm->execute(array($ID, $original, $shorten, $linkID, $active, $views, $date));
        if ($check) {
            $msg = "<p class=\"success\">Url Created</p>
            <a href=\"{$check}.php\" target='_blank'>{$baseUrl}{$check}</a>";
            rename('temp.php', $check.'.php');
            createFile();
        } else {
            $msg = "<p class=\"error\">Invalid Url</p>";
        }
        //cleaner function part
        $clean = $bdd->prepare("DELETE FROM urllinks WHERE Shorten = '.php'"); //deletes all empty $_POST sent
        $clean->execute();
    }
    function createFile(){ // here we create a file that deletes itself after being useds
        $temp = fopen('temp.php', 'w');
        $txt = '<?php 
                    header("location: " . $_COOKIE["UrlCookie"]);
                    $fileName = basename($_SERVER["PHP_SELF"]);
                    unlink($fileName);
        ?> ';
        file_put_contents('temp.php', $txt);
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your profile - MiniLink</title>
    <link rel="stylesheet" href="../styles/main.css"/>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="../img/favicon.png"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <style>
        body{
            overflow:hidden;
        }
        @media only screen and (min-width: 768px){
            input[type="url"]{
                width:200px;
            }
        }
        @media only screen and (min-width: 600px){
            input[type="url"]{
                width:180px;
            }
        }
    </style>
    <?php include 'headerProfil.php'; ?>
    <div class="main">
        <div class="profileContainer1">
        <h2 class="name">Nice to see you again, <?php echo $userinfo['username']; ?></h2>
            <?php echo $msg;?>
        <form action="#" method="post">
        <label class="contextLink" for="contextLink">Enter your loo<strong class="orange">o</strong>oooo<strong class="red">o</strong>ng link here !</label> <br>
            <input type="url" name="url" placeholder=" "> <br>
            <input class="submitSignin" type="submit" name="submit" value="Make it mini">
        </form>
        </div>
        <div class="profileContainer2">
        <h2 class="linkHistory">Your <strong class="orange">Mini</strong><strong class="red">Link</strong> history</h2>
            <div class="subWrapper">
                <?php $resultLink = $bdd->query("SELECT Shorten, Active, Views, linkID, Original FROM urllinks WHERE id = $getid");
                while($row = $resultLink->fetch(PDO::FETCH_ASSOC)){
                    if($row['Shorten']!= ".php"){//does not return empty links
                        //oh boi do im proud of this code
                            $linkID = $row['linkID'];
                            if(isset($_POST[$linkID .'on'])){
                                $resultActive = $bdd->prepare("UPDATE urllinks SET Active = 1 WHERE LinkID = $linkID" );
                                $resultActive->execute();
                            }
                            elseif(isset($_POST[$linkID .'off'])){
                                $resultActive = $bdd->prepare("UPDATE urllinks SET Active = 0 WHERE LinkID = $linkID" ); 
                                $resultActive->execute();
                            }
                            $form = "<form action ='#' method ='post' class='formSwitcher form_on'>
                                        <input class='phpFormOnOff' type='submit' name='".$linkID."on' value='✔️' title='Turn On'>
                                    </form>";
                            $form1 = "<form action ='#' method ='post' class='formSwitcher form_off'>
                                        <input class='phpFormOnOff' type='submit' name='".$linkID."off' value='❌' title='Turn Off'>
                                    </form>";
                        echo "<div class='table'>
                                <div class='linkTable'><a href='". $row['Shorten']. "' target='_blank' class='links' title='".$row['Original']."'>". $row['Shorten']. "<a/></div>"; 
                        echo "<div class='views'><p class='viewsText'><img src='../img/view.png' title='viewIcon' alt='viewIcon'/>"." ". $row['Views'] ."</p></div> ";
                        echo "<div class='forms'>".$form . $form1 . "</div>" ;   
                    }
                    
                    echo "</div>";
                    
                } 
                    ?>
                    </div>
        </div>
         <div class="randomFrame frame3"></div>
         <div class="randomFrame frame4"></div>
         <div class="randomFrame frame5"></div>
         <div class="randomFrame frame6"></div>
    </div>
    
    <?php include 'footer.php';?>
    <script src="../scripts/script.js"></script>
</body>
</html>

