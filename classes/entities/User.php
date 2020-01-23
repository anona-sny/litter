<?php

require_once(__DIR__."/../services/db.php");
require_once(__DIR__."/Post.php");

class User {

    public $id;
    public $username;
    public $email;
    public $verified;
    public $schemaNumber;
    public $fontSize;
    public $followers;
    public $follows;
    public $profilePhoto;
    public $about;
    public $followsAccounts = [];
    public $followedAccounts = [];
    public $postCount = 0;


    /**
     * User constructor.
     * Natáhne data z DB podle daného ID
     * @param $username string uživatelské jméno
     * @param $private bool pokud na uživatele kouká jiný uživael, nezobrazí private data, true = zobrazí
     */
    public function __construct($username, $private) {
        $this->setUsername($username, $private);
    }

    private function setUsername($username, $private){
        $db = DB::getInstance();
        $sql = "SELECT users.id as 'id', username,email,email_verified,schema_type,font_size FROM users left join settings on users.id=settings.user_id WHERE users.username='$username'";
        $userDetails = $db->SELECT($sql);
        if(sizeof($userDetails) == 0){
            $this->username = "";
            return;
        }
        $row = $userDetails[0];
        $this->followers = 55;
        $this->follows = 64;
        // Veřejná data
        $this->username = $row["username"];
        $this->id = $row["id"];
        $this->verified = boolval($row["email_verified"]);
        $mainpageSql = "SELECT profile_name, image_background, user_photo,about FROM users LEFT JOIN mainpages m on users.id = m.user_id WHERE users.id = ".$this->id.";";
        $mainpageDetail = $db->SELECT($mainpageSql);
        $this->profilePhoto = $mainpageDetail[0]["user_photo"];
        $this->about = base64_decode($mainpageDetail[0]["about"]);
        $postCountSql = "SELECT count(*) as 'count' FROM posts WHERE user_id=".$this->id;
        $postCount = $db->SELECT($postCountSql);
        $this->postCount = $postCount[0]["count"];
        if($this->profilePhoto == null || $this->profilePhoto == ""){
            $this->profilePhoto = "../images/avatar.png";
        } else {
            $this->profilePhoto = "../uploaded/".$this->profilePhoto;
        }
        // Privátní data
        if(boolval($private) === true){
            $this->email = $row["email"];
            $this->fontSize = intval($row["font_size"]);
            $this->schemaNumber = intval($row["schema_type"]);
        }
    }

    public function getPosts($count = 10, $offset = 0) {
        $db = DB::getInstance();
        $array = [];
        $sql = "SELECT users.username as 'author', posts.id as 'id', posts.header as 'header', posts.text as 'text', posts.image as 'image' FROM users LEFT JOIN posts ON users.id=posts.user_id WHERE user_id=".$this->id." ORDER BY posts.id DESC LIMIT $count OFFSET $offset";
        $res = $db->SELECT($sql);
        if(sizeof($res)){
            for ($i = 0; $i<sizeof($res); $i++){
                $sqlLikes = "SELECT count(*) as 'count' FROM likes WHERE post_id = ".$res[$i]["id"];
                $post = new Post($res[$i]["id"], base64_decode($res[$i]["header"]), base64_decode($res[$i]["text"]), $res[$i]["image"], $res[$i]["author"], $db->SELECT($sqlLikes)[0]["count"]);
                array_push($array, $post);
            }
        }
        return $array;
    }

    public function getWallPosts(){
        $db = DB::getInstance();
        $array = [];
        $sql = "SELECT posts.id as 'id', posts.header as 'header', posts.text as 'body', posts.image as 'image', users.username as 'author' FROM posts LEFT JOIN users ON posts.user_id=users.id WHERE posts.user_id IN(SELECT follows.followed FROM follows WHERE follows.user_id=".$_SESSION["id"].") ORDER BY posts.id DESC;";
        $res = $db->SELECT($sql);
        if(sizeof($res)){
            for ($i = 0; $i<sizeof($res); $i++){
                $sqlLikes = "SELECT count(*) as 'count' FROM likes WHERE post_id = ".$res[$i]["id"];
                $post = new Post($res[$i]["id"], base64_decode($res[$i]["header"]), base64_decode($res[$i]["body"]), $res[$i]["image"], $res[$i]["author"], $db->SELECT($sqlLikes)[0]["count"]);
                array_push($array, $post);
            }
        }
        return $array;
    }

    public function getFollows(){
        $db = DB::getInstance();
        $this->follows = [];
        $sqlFollow = "SELECT * FROM follows LEFT JOIN users ON follows.followed=users.id WHERE follows.user_id=".$this->id;
        $sqlFollowed = "SELECT * FROM follows LEFT JOIN users ON follows.user_id=users.id WHERE follows.followed=".$this->id;
        $res = $db->SELECT($sqlFollow);
        if(sizeof($res)){
            for ($i = 0; $i<sizeof($res); $i++){
                $user = new User($res[$i]["username"], false);
                array_push($this->followsAccounts, $user);
            }
        }
        $res = $db->SELECT($sqlFollowed);
        if(sizeof($res)){
            for ($i = 0; $i<sizeof($res); $i++){
                $user = new User($res[$i]["username"], false);
                array_push($this->followedAccounts, $user);
            }
        }
    }

    public function alreadyFollowing($username){
        $fol = false;
        for($i = 0; $i < count($this->followsAccounts); $i++){
            if($this->followsAccounts[$i]->username == $username){
                $fol = true;
            }
        }
        return $fol;
    }

    public static function saveNewImage($name){
        $db = DB::getInstance();
        $sql = "UPDATE mainpages SET mainpages.user_photo = '".$name."' WHERE user_id=".$_SESSION["id"].";";
        $db->UPDATE($sql);
    }

    public static function saveNewArticle($header, $body, $image){
        $db = DB::getInstance();
        $sql = "INSERT INTO posts(user_id, header, text, image) VALUES (".$_SESSION["id"].", '".base64_encode($header)."', '".base64_encode($body)."', ".($image==null?"NULL":"'".$image."'").")";
        $db->INSERT($sql);
    }

    public static function deleteArticle($id_article, $id_user) {
        $db = DB::getInstance();
        $sql = "SELECT count(*) as 'pocet' FROM posts WHERE id=".$id_article." AND user_id=".$id_user.";";
        if(intval($db->SELECT($sql)[0]["pocet"]) > 0) {
            $sql = "DELETE FROM posts WHERE id=".$id_article." AND user_id=".$id_user.";";
            $db->DELETE($sql);
            return "true";
        } else {
            return "access denied";
        }
    }

    public static function likeArticle($id_article, $id_user){
        $db = DB::getInstance();
        $sql = "SELECT count(*) as 'count' FROM likes WHERE user_id=".$id_user." AND post_id=".$id_article;
        if(intval($db->SELECT($sql)[0]["count"]) > 0) {
            $sql = "DELETE FROM likes WHERE  user_id=".$id_user." AND post_id=".$id_article;
            $db->DELETE($sql);
            return "unliked";
        } else {
            $sql = "INSERT INTO likes(user_id, post_id) VALUES(".$id_user.",".$id_article.")";
            $db->INSERT($sql);
            return "liked";
        }
    }

    public static function getUsersByName($username, $number, $offset) {
        $db = DB::getInstance();
        $sql = 'SELECT username FROM users WHERE username LIKE "%'.$username.'%" LIMIT '.$number.' OFFSET '.$offset.';';
        $users = [];
        $res = $db->SELECT($sql);
        if(sizeof($res)){
            for ($i = 0; $i<sizeof($res); $i++){
                $user = new User($res[$i]["username"], false);
                array_push($users, $user);
            }
        }
        return $users;
    }

    public static function getUsersCountByName($username) {
        $db = DB::getInstance();
        $sql = "SELECT count(*) as 'count' FROM users WHERE username LIKE \"%".$username."%\";";
        $res = $db->SELECT($sql);
        return $res[0]["count"];
    }


}