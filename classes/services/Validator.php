<?php

class Validator {

    public function testPassword($password) {
        if (preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$/", $password)) {
            return true;
        }
        return false;
    }

    public function testUsername($username) {
        if (preg_match("/^[A-Za-z0-9_]{1,25}$/", $username)) {
            return true;
        }
        return false;
    }

    public function testEmail($email) {
        if (preg_match("/[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/", $email)) {
            return true;
        }
        return false;
    }

}