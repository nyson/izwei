<?php

require_once("./lib/SQL.php");

/**
 * manages functions of getting images
 * @package i
 */
class Search {
    private $offset;
    private $count;
    private $result;

    public function __construct() {
        $this->setSpectrum(); // set spectrum to default
    }

    public function get() {
        return $this->result->fetch_object();
    }

    public function search() {
        $this->result = SQL::query(
            "SELECT * FROM images LIMIT $this->offset, $this->count");
    }

    public function setSpectrum($offset=0, $count=12) {
        $this->offset = $offset;
        $this->count = $count;
    }

    public function setTags($tags) {
        
    }
}
?>
