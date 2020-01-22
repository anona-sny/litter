<?php

require_once(__DIR__."/../services/db.php");
require_once(__DIR__."/../entities/ErrorResponse.php");
require_once(__DIR__."/../entities/Response.php");
require_once(__DIR__."/../entities/User.php");
require_once(__DIR__."/../services/Logger.php");

/**
 * Class RequestRobot
 * Manage requests and process them and return JSON string as result
 * Works as model-controller in MVC
 */
class RequestRobot {

    private $db;

    function __construct() {
        $this->db = DB::getInstance();
    }

    public function register($username, $password, $email, $image){
        $existUser = $this->db->SELECT("SELECT username FROM users WHERE username='".$username."'");
        if(count($existUser) > 0){
            return false;
        } else {
            $passwordHash = hash("SHA512", "dta".$password."pet");
            $createUser = $this->db->INSERT("INSERT INTO users(username, password_hash, email) VALUES ('$username', '$passwordHash', '$email')");
            if($createUser) {
                $id = $this->db->SELECT("SELECT id FROM users WHERE username='$username'")[0]["id"];
                if($image != "NULL"){
                    $image = "'$image'";
                }
                $this->db->INSERT("INSERT INTO mainpages(user_id, profile_name, image_background, user_photo) VALUES($id, '$username', NULL, $image)");
                $this->db->INSERT("INSERT INTO settings(user_id, schema_type, font_size) VALUES($id, 1, 1)");
                $this->db->INSERT("INSERT INTO follows(user_id, followed) VALUE (".$id.", ".$id.");");
                return true;
            }
            return false;
        }
    }

    public function savePhoto() {
        $target_dir = __DIR__."/../../uploaded/";
        $imageFileType = strtolower(pathinfo(basename($_FILES["profile-photo"]["name"]),PATHINFO_EXTENSION));
        $filename = $this->generateRandomString() . "." . $imageFileType;
        $target_file = $target_dir . $filename;
        $uploadOk = 1;
        if(isset($_POST["submit"])) {
            $check = getimagesize($_FILES["profile-photo"]["tmp_name"]);
            if($check == false) {
                return false;
            }
        }
        if (file_exists($target_file)) {
            return false;
        }
        if ($_FILES["profile-photo"]["size"] > 5000000) {
            return false;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            return false;
        }
        if ($uploadOk == 0) {
            return false;
        } else {
            if (move_uploaded_file($_FILES["profile-photo"]["tmp_name"], $target_file)) {
                return $filename;
            } else {
                return false;
            }
        }
    }

    function generateRandomString($length = 36) {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    /**
     * @param $command string command to process
     * @param $data string data for process
     * @return false|string
     */
    function proceed($command, $data) {
        if($command == "getUserData") {
            return $this->getUserData();
        } else if ($command == "changeUserData" and $data != null) {
            return $this->saveNewData();
        } else if($command == "saveImage") {
            return $this->saveNewImage();
        } else if($command == "saveArticle" and $data != null) {
            return $this->saveArticle($data);
        } else if($command == "deleteArticle" and $data != null){
            return $this->deleteArticle($data);
        } else if($command == "follow" and $data != null){
            return $this->follow($data);
        } else {
            return $this->errorMessage("request not found");
        }
    }

    function login($data){
        $obj = json_decode($data);
        $username = $obj->username;
        $password = $obj->password;
        $logData = $this->db->SELECT(sprintf("SELECT id, username, password_hash FROM users WHERE username='%s'", $username));
        if(count($logData) != 0){
            if($logData[0]["password_hash"] == hash("SHA512", "dta".$password."pet")) {
                session_unset();
                $_SESSION["id"] = $logData[0]["id"];
                $_SESSION["username"] = $logData[0]["username"];
                return $this->convertToJSON(new Response("logged"));
            } else {
                return $this->errorMessage("Password invalid");
            }
        } else {
            return $this->errorMessage("Username dont't exist");
        }
    }

    function follow($data){
        $obj = json_decode($data);
        $username = mysqli_real_escape_string(DB::getInstance()->getConnection(), htmlspecialchars($obj->username));
        $sqlActiveFollow = "SELECT users.id as 'id' FROM follows LEFT JOIN users ON follows.followed = users.id WHERE username='".$username."' AND follows.user_id = ".$_SESSION["id"].";";
        $data = $this->db->SELECT($sqlActiveFollow);
        if(count($data) > 0){
            $sqlRemove = "DELETE FROM follows WHERE followed=".$data[0]["id"]." AND user_id=".$_SESSION["id"];
            Logger::getInstance()->write($sqlRemove);
            if($this->db->DELETE($sqlRemove)) {
                return "removed";
            } else {
                return "error";
            }
        } else {
            $usernameSQL = "SELECT id FROM users WHERE username='$username'";
            Logger::getInstance()->write($usernameSQL);
            $usrArray = $this->db->SELECT($usernameSQL);
            if(count($usrArray) > 0) {
                $sqlAdd = "INSERT INTO follows (user_id, followed) VALUES(".$_SESSION["id"].", ".$usrArray[0]["id"].")";
                Logger::getInstance()->write($sqlAdd);
                if($this->db->INSERT($sqlAdd)) {
                    return "added";
                } else {
                    return "error";
                }
            } else {
                return "error";
            }
        }
    }

    function saveNewData(){
        $obj = json_decode($_POST["data"]);
        if(isset($obj->newPassword)){
            $vysl = $this->db->SELECT("SELECT password_hash FROM users WHERE id=".$_SESSION["id"]);
            $h = hash("SHA512", "dta".$obj->oldPassword."pet");
            if($vysl[0]["password_hash"] == $h){
                $h2 = hash("SHA512", "dta".$obj->newPassword."pet");
                $this->db->UPDATE("UPDATE users SET password_hash='".$h2."' WHERE id=".$_SESSION["id"]);
            } else {
                return "bad password";
            }
        }
        $sqlUpdate = "UPDATE mainpages SET about='".base64_encode($obj->aboutme)."' WHERE user_id=".$_SESSION["id"];
        $this->db->UPDATE($sqlUpdate);
        return "true";
    }


    /**
     * Join all data about user and merge them into one object, then parse to JSON string and return
     * @return false|string
     */
    function getUserData() {
        $id = $_SESSION["id"];
        $user = new User($id, true);
        if(strlen($user->username) == 0){
            return $this->errorMessage("Object with id=".$id." have no data");
        }
        return $this->convertToJSON($user);
    }

    function errorMessage($text) {
        $err = new ErrorResponse($text);
        return json_encode($err);
    }

    function convertToJSON($object) {
        try {
            return json_encode($object, 0, 5);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage());
        }
    }

    function saveNewImage(){
        if(isset($_FILES["file"])){
            $target_dir = __DIR__."/../../uploaded/";
            $imageFileType = strtolower(pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));
            $filename = $this->generateRandomString() . "." . $imageFileType;
            $target_file = $target_dir . $filename;
            $uploadOk = 1;
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            if($check == false) {
                return "false";
            }
            if (file_exists($target_file)) {
                return "false";
            }
            if ($_FILES["file"]["size"] > 5000000) {
                return "false";
            }
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                return "false";
            }
            if ($uploadOk == 0) {
                return "false";
            } else {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    User::saveNewImage($filename);
                    return "true";
                } else {
                    return "false";
                }
            }
        }
        return false;
    }

    public function saveArticle($data){
        $obj = json_decode($data);
        $header = $obj->header;
        $body = $obj->body;
        $image = null;
        if(strlen($header) < 3){
            return "header short";
        }
        if(isset($_FILES["file"])){
            $target_dir = __DIR__."/../../uploaded/";
            $imageFileType = strtolower(pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));
            $filename = $this->generateRandomString() . "." . $imageFileType;
            $target_file = $target_dir . $filename;
            $uploadOk = 1;
            $check = getimagesize($_FILES["file"]["tmp_name"]);
            if($check == false) {
                return "image corrupted";
            }
            if (file_exists($target_file)) {
                return "image corrupted";
            }
            if ($_FILES["file"]["size"] > 5000000) {
                return "image corrupted";
            }
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                return "image corrupted";
            }
            if ($uploadOk == 0) {
                return "image corrupted";
            } else {
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                    $image = $filename;
                } else {
                    return "image corrupted";
                }
            }
        }
        USER::saveNewArticle($header, $body, $image);
        return "true";
    }

    public function deleteArticle($data) {
        $obj = json_decode($data);
        $id_post = $obj->idPost;
        $id_user = $_SESSION["id"];
        return User::deleteArticle($id_post, $id_user);
    }
}