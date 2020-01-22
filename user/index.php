<?php

session_start();
require_once __DIR__."/../classes/entities/User.php";
$username = "not exist user";
$followers = -1;
$follows = -1;
$posts = [];
$profilePhoto = "";
$isMine = false;
$about =   "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
        Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
        sunt in culpa qui officia deserunt mollit anim id est laborum.";
if(isset($_GET["u"])&&!empty($_GET["u"])){
    $userN = htmlspecialchars(trim($_GET["u"]));
    $user = new User($userN, false);
    if($user->username != ""){
        $username = $user->username;
        $follows = $user->follows;
        $followers = $user->followers;
        $posts = $user->getPosts();
        $profilePhoto = $user->profilePhoto;
        $about = $user->about;
        if(isset($_SESSION["id"])&&$user->id == $_SESSION["id"]){
            $isMine = true;
        }
    }
} else {
    header("Location: ../");
}


?>
<html>
    <head>
        <title>Litter</title>
        <link rel="stylesheet" href="../css/user.css">
        <meta charset="utf-8">
        <meta http-equiv="Content-language" content="cs">
        <meta name="viewport" content="width=device-width, initial-scale=1" viewport-fit="cover">
        <script src="../js/jquery-3.4.1.min.js"></script>
        <script src="../js/user.js"></script>
        <noscript>Your browser does not support JavaScript!</noscript>
    </head>
    <body>
        <?php
        if($isMine){
            include("changer.php");
        } else {
            include("viewer.php");
        }

        ?>
    </body>
</html>