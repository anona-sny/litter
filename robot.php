<?php
/**
 * Zde budou metody a třídy pro práci s background přenosem dat
 * nahrávání přimo objektů do FE
 * ukládání dat z FE
 * Přenos v JSON (json_encode, json_decode)
 * 
 */
session_start();
require_once(__DIR__."/classes/services/RequestRobot.php");
$reqR = new RequestRobot();
if(isset($_SESSION["id"])) {
    if(isset($_POST["command"]) and $_POST["command"] == "login") {
        session_destroy();
        print($reqR->login($_POST["data"]));
    } else if(isset($_POST["command"]) and isset($_POST["data"])) {
        print($reqR->proceed($_POST["command"], $_POST["data"]));
    } else {
        print($reqR->errorMessage("command or data not setted"));
    }
} else {
    if(isset($_POST["command"]) and $_POST["command"] == "login") {
        print($reqR->login($_POST["data"]));
    } else {
        print("{\"error\": \"id is not logined\"}");
    }
}

