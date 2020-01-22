<?php

/**
 * Class Post Třída která uchovává data jednoho postu
 */
class Post {

    public $id;
    public $author;
    public $header;
    public $body;
    public $image;

    public function __construct($id, $header, $text, $image, $author) {
        $this->header = $header;
        $this->body = $text;
        $this->id = $id;
        $this->image = $image;
        $this->author = $author;
    }
}