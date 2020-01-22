<?php

class Logger {

    private static $instance = null;
    private $file;

    private function __construct() {

    }

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    public function write($text) {
        $file = fopen(__DIR__."/../../log.txt", "a");
        fwrite($file, $text."\n");
        fclose($file);
    }
}