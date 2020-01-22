<?php


class Response {

    public $time;
    public $data;

    function __construct($data) {
        $this->data = $data;
        $this->time = date("d-m-Y H:i:s");
    }
}