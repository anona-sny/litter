<?php
    require_once __DIR__."/../classes/services/Validator.php";
    require_once __DIR__."/../classes/services/RequestRobot.php";
    require_once __DIR__."/../classes/services/db.php";

    $validation = new Validator();
    $robot = new RequestRobot();
    $db = DB::getInstance();

    $error = "";

    if(isset($_POST["lookusernames"])){
        if(isset($_POST["username"])&&$validation->testUsername($_POST["username"])){
            $username = htmlspecialchars($_POST["username"]);
            $existUser = $db->SELECT("SELECT username FROM users WHERE username='".$username."'");
            if(count($existUser) > 0){
                echo "false";
            } else {
                echo "true";
            }
        } else {
            echo "false";
        }
        exit();
    }

    if(isset($_POST["registration"])) {
        if(isset($_POST["username"])&&!empty($_POST["username"])&&$validation->testUsername($_POST["username"])) {
            if(isset($_POST["password"])&&!empty($_POST["password"])&&isset($_POST["password-again"])&&!empty($_POST["password-again"])&&$validation->testPassword($_POST["password"])) {
                if($_POST["password"] == $_POST["password-again"]) {
                    if(isset($_POST["email"])&&!empty($_POST["email"])&&$validation->testEmail($_POST["email"])) {
                        $username = htmlspecialchars($_POST["username"]);
                        $password = htmlspecialchars($_POST["password"]);
                        $email = htmlspecialchars($_POST["email"]);
                        $image = "NULL";
                        if(isset($_FILES["profile-photo"])){
                            $val = $robot->savePhoto();
                            if($val !== false){
                                $image = $val;
                            }
                        }
                        if($robot->register($username, $password, $email, $image)) {
                            header("Location: ../");
                        } else {
                            $error = "username already exist";
                        }
                    } else {
                        // email not valid
                        $error = "email not valid";
                    }
                } else {
                    //passwords not equal
                    $error = "password not valid";
                }
            } else {
                //password 1 or 2 empty
                $error = "password empty or not same";
            }
        } else {
            // username empty
            $error = "username not valid";
        }
    }
?>
<html>
    <head>
        <title>Litter - Registration</title>
        <link rel="stylesheet" href="../css/registration.css">
        <meta charset="utf-8">
        <meta http-equiv="Content-language" content="cs">
        <meta name="viewport" content="width=device-width, initial-scale=1" viewport-fit="cover">
        <link rel="preload" href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,600,700&display=swap" as="style">
        <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
        <noscript>Your browser does not support JavaScript!</noscript>
    </head>
    <body>
        <div id="only-js">
            <form id="center" method="post" action="index.php" onsubmit="return checkForm()" enctype="multipart/form-data">
                <h1 id="registration-header">Registrace</h1>
                <div class="registration-group">
                    <label for="username-input" id="label-icon-username" class="input-icon"></label>
                    <input class="input" type="text" name="username" id="username-input" placeholder="Zadejte uživatelské jméno" required pattern="^[A-Za-z0-9_]{1,25}$">
                </div>
                <div class="registration-group">
                    <label for="password-input" class="input-icon" id="label-icon-password"></label>
                    <input class="input" type="password" name="password" id="password-input" placeholder="Zadejte heslo" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$">
                </div>
                <div class="registration-group">
                    <label for="password-input-again" class="input-icon" id="label-icon-password"></label>
                    <input class="input" type="password" name="password-again" id="password-input-again" placeholder="Zadejte heslo znovu" required pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$">
                </div>
                <div class="registration-group">
                    <label for="email-input" class="input-icon" id="label-icon-email"></label>
                    <input class="input" type="email" name="email" id="email-input" placeholder="Zadejte platný email" required>
                </div>
                <div class="registration-group short-group">
                    <label for="profile-photo" id="label-file">Vybrat profilovou fotku</label>
                    <input type="file" onchange="readURL(this);" id="profile-photo" name="profile-photo">
                </div>
                <div id="profile-photo-preview"></div>
                <a href="../" id="back-button">Zpět</a>
                <button type="submit" id="registration" name="registration">Zaregistrovat</button>
            </form>
        </div>
    </body>
    <script src="../js/registration.js"></script>
    <script>
        <?php
            if($error != ""){
                echo "alert(\"".$error."\");";
            }
        ?>
    </script>
</html>
