<?php

session_start();
require_once __DIR__."/../classes/entities/User.php";


if(!isset($_SESSION["id"])){
    header("Location: ../");
    exit();
}

$query = "";
$queryREGEX = '/^[a-zA-Z0-9]{1,25}$/';
$user = new User($_SESSION["username"], true);
$user->getFollows();
if(isset($_GET["query"])) {
    $query = mysqli_real_escape_string(DB::getInstance()->getConnection(), htmlspecialchars($_GET["query"]));
}
if(!preg_match($queryREGEX, $query)){
    $query = "";
}
$page = 0;
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    header("Location: ?query=".$query."&page=1");
    exit();
}
$no_of_records_per_page = 5;
$offset = ($page-1) * $no_of_records_per_page;
$result = User::getUsersByName($query, $no_of_records_per_page, $offset);
$total_rows = User::getUsersCountByName($query);
$total_pages = ceil($total_rows / $no_of_records_per_page);

//


?>

<html>
<head>
    <title>Litter</title>
    <link rel="stylesheet" href="../css/search.css">
    <meta charset="utf-8">
    <meta http-equiv="Content-language" content="cs">
    <meta name="viewport" content="width=device-width, initial-scale=1" viewport-fit=cover">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</head>
<body>
<main>
    <div id="main-center">
        <div id="main-col">
        <?php
            if(count($result) > 0){
                for($i = 0; $i < count($result); $i++){
                    echo '<div class="user-ticket">';
                    echo '<div class="user-image" style="background-image: url(\''.$result[$i]->profilePhoto.'\')" onclick="window.location.href = \'../user/?u='.$result[$i]->username.'\'"></div>';
                    echo '<div class="user-username">'.$result[$i]->username.'</div>';
                    echo '<div class="user-follow" onclick="follow(\''.$result[$i]->username.'\', this)">'.($user->alreadyFollowing($result[$i]->username)?"Nesledovat":"Sledovat").'</div>';
                    echo '<div class="user-posts">Počet příspěvků: '.$result[$i]->postCount.'</div>';
                    echo '</div>';
                }
            }
        ?>
            <div id="pagination">
                <?php
                for($i = 1; $i <= $total_pages; $i++){
                    echo "<a class='pag-item' href='?query=".$query."&page=".$i."'> ".$i." </a>";
                }
                ?>
            </div>
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
            <div class="mobile-menu-item">Přátelé</div>
            <div class="mobile-menu-item" href="../logout.php">Odhlásit</div>
        </div>
    </div>
    <div id="bar-menu">
        <div onclick="window.location.href = '../wall/'" class="menu-item">Zeď</div>
        <div class="menu-item">Přátelé</div>
        <div class="menu-item" href="../logout.php">Odhlásit</div>
    </div>
    <div id="bar-search">
        <input type="text" id="search-input" placeholder="Zadejte jméno">
        <button id="search-button">Vyhledat</button>
    </div>
</header>
</body>
<script src="../js/search.js"></script>
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
