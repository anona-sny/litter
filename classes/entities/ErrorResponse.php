<?php

class ErrorResponse {

    public $error;

    function __construct($text) {
        $this->error = $text;
    }
}