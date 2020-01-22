<?php

session_start();
require_once __DIR__."/../classes/entities/User.php";


if(!isset($_SESSION["id"])){
    header("Location: ../");
    exit();
}

$user = new User($_SESSION["username"], true);
$user->getFollows();
$follows = $user->followsAccounts;

?>

<html>
<head>
    <title>Litter</title>
    <link rel="stylesheet" href="../css/friends.css">
    <meta charset="utf-8">
    <meta http-equiv="Content-language" content="cs">
    <meta name="viewport" content="width=device-width, initial-scale=1" viewport-fit=cover">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</head>
<body>
<main>
    <div id="main-center">
        <div id="main-col">
        <div id="nazev-stranky">Seznam sledujících</div>
        <?php
            if(count($follows) > 0){
                for($i = 0; $i < count($follows); $i++){
                    echo '<div class="user-ticket">';
                    echo '<div class="user-image" style="background-image: url(\''.$follows[$i]->profilePhoto.'\')" onclick="window.location.href = \'../user/?u='.$follows[$i]->username.'\'"></div>';
                    echo '<div class="user-username">'.$follows[$i]->username.'</div>';
                    echo '<div class="user-follow" onclick="follow(\''.$follows[$i]->username.'\', this)">'.($user->alreadyFollowing($follows[$i]->username)?"Nesledovat":"Sledovat").'</div>';
                    echo '<div class="user-posts">Počet příspěvků: '.$follows[$i]->postCount.'</div>';
                    echo '</div>';
                }
            }
        ?>
        </div>
    </div>
    <footer>

    </footer>
</main>
<header id="bar">
    <div id="bar-avatar"></div>
    <div id="bar-avatar-with-menu">
        <div id="mobile-menu">
            <div class="mobile-menu-item" id="profile-redirect">Profil</div>
            <div class="mobile-menu-item" onclick="window.location.href = '../wall/'">Zeď</div>
            <div class="mobile-menu-item" onclick="window.location.href = '../friends/'">Přátelé</div>
            <div class="mobile-menu-item" href="../logout.php">Odhlásit</div>
        </div>
    </div>
    <div id="bar-menu">
        <div onclick="window.location.href = '../wall/'" class="menu-item">Zeď</div>
        <div class="menu-item" onclick="window.location.href = '../friends/'">Přátelé</div>
        <div class="menu-item" href="../logout.php">Odhlásit</div>
    </div>
    <div id="bar-search">
        <input type="text" id="search-input" placeholder="Zadejte jméno">
        <button id="search-button">Vyhledat</button>
    </div>
</header>
</body>
<script src="../js/friends.js"></script>
<script>
    let username = '<?php echo $_SESSION["username"]; ?>';
    let profilePhoto = '<?php echo $user->profilePhoto; ?>';
    $("#profile-redirect").on("click",function(){
        window.location.href = "../user/?u="+username;
    });
    $("#bar-avatar").on("click",function(){
        window.location.href = "../user/?u="+username;
    });

    $("#bar-avatar").css("background-image", "url('"+profilePhoto+"')");
    $("#bar-avatar-with-menu").css("background-image", "url('"+profilePhoto+"')");

</script>
</html>
