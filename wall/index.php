<?php

session_start();
require_once __DIR__."/../classes/entities/User.php";


if(!isset($_SESSION["id"])){
    header("Location: ../");
    exit();
}
$user = new User($_SESSION["username"], true);
$wall_posts = $user->getWallPosts();



?>

<html>
<head>
    <title>Litter</title>
    <link rel="stylesheet" href="../css/wall.css">
    <meta charset="utf-8">
    <meta http-equiv="Content-language" content="cs">
    <meta name="viewport" content="width=device-width, initial-scale=1" viewport-fit=cover">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</head>
<body>
<main>
    <div id="main-center">
        <div id="main-col">
            <h2>Příběhy <div id="add-story">Přidat</div></h2>
            <?php
            for($i = 0; $i < count($wall_posts); $i++) {
                $data = $wall_posts[$i];
                echo '<article>
                    <article-author>By <a href="../user/?u='.$data->author.'">'.$data->author.'</a></article-author>
                    <article-header>'.$data->header.'</article-header>
                    <article-body>'.$data->body.'</article-body>
                    <article-image style="background-image: url(\'../uploaded/'.$data->image.'\'); '.($data->image==null?'display:none':'display:block').'"></article-image>
                    <article-footer>
                        <div class="share-button"></div>
                        <div class="like-button"></div>
                    </article-footer>
                </article>';
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
            <div class="mobile-menu-item">Zeď</div>
            <div class="mobile-menu-item">Přátelé</div>
            <div class="mobile-menu-item" href="../logout.php">Odhlásit</div>
        </div>
    </div>
    <div id="bar-menu">
        <a href="#" class="menu-item">Zeď</a>
        <a class="menu-item">Přátelé</a>
        <a class="menu-item" href="../logout.php">Odhlásit</a>
    </div>
    <div id="bar-search">
        <input type="text" id="search-input" placeholder="Zadejte jméno">
        <button id="search-button">Vyhledat</button>
    </div>
</header>
<div id="new-article-shadow">
    <div id="new-article-frame">
        <input type="text" id="new-article-header" placeholder="Název článku sem" min="3" required>
        <textarea id="new-article-body" placeholder="Text článku sem (až 2000 znaků)"></textarea>
        <label for="new-article-file" id="new-article-file-label">Přidat obrázek</label>
        <input type="file" id="new-article-file">
        <div id="new-file-back">Zpět</div>
        <div id="new-file-submit">Zveřejnit</div>
    </div>
</div>
</body>
<script src="../js/wall.js"></script>
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
