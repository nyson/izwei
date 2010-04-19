<?php

require_once("./lib/SQL.php");

/**
 * manages functions of getting images
 * @package i
 */
class Search {
    /**
     * offset of the search
     * @var int $offset
     */
    private $offset;
    /**
     * amount of rows to get
     * @var int $count
     */
    private $count;

    /**
     * associative array of the objects in the row
     */
    private $query;






    /**
     * creates default query "SELECT * FROM images LIMIT 0, 12"
     */
    public function __construct() {
        $this->setSpectrum();
        $this->query['base'] = "SELECT * FROM images";
        $this->setOrder();
    }

    public function setOrder($orderType = "default") {
        switch(strtolower($orderType)){
            case 'random':
                $this->query['order'] = "ORDER BY RAND()";
                break;

            default:
                $this->query['order'] = "";
        }
    }

    /**
     * returns a mysqli_result from created query
     * @return mysqli_result
     */
    public function search() {
        $query = $this->query['base'] . " " . $this->query['order'] . " "
            . $this->query['limit'];
        return SQL::query($query);
    }

    public function setSpectrum($offset=0, $count=12) {
        $this->query['limit'] = "LIMIT $offset, $count";
    }

    public function setTags($tags) {
        
    }
}
?>
