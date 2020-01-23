<header>
    <div id="back-home">Zpět</div>
    <div id="profile-group">
        <?php
        echo '<div id="profile-photo" style="background-image: url('.$profilePhoto.')"></div>';
        ?>
        <div id="profile-name"><?php echo $username; ?></div>
    </div>
</header>
<main>
    <div class="foll-side">Sledující: <?php echo $followers; ?></div><div class="foll-side">Sleduji: <?php echo $follows; ?></div>
    <div id="about-me">O mně</div>
    <div id="about-me-text"><?php echo $about; ?></div>
    <div id="leets">Příspěvky</div>
    <div id="leets-array">
        <?php
        for($i = 0; $i<count($posts);$i++){
            echo '<article>';
            echo '<article-author>By <a href="?u='.$posts[$i]->author.'">'.$posts[$i]->author.'</a></article-author>';
            echo '<article-header>'.$posts[$i]->header.'</article-header>';
            echo '<article-body>'.$posts[$i]->body.'</article-body>';
            echo '<article-image style="background-image: url(\'../uploaded/'.$posts[$i]->image.'\'); '.($posts[$i]->image==null?'display:none':'display:block').'"></article-image>';
            echo '<article-footer><div class="like-button" post-id="'.$posts[$i]->id.'" onclick="likeTweet(this)"></div><div class="like-count">'.$posts[$i]->likes.'</div></article-footer>';
            echo '</article>';
        }
        ?>
    </div>
</main>
<footer>
    Created by me
</footer>