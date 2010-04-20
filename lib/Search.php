<?php

/**
 * manages functions of getting images
 * @package i
 */


/**
 * sql bindings for i
 */
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
     * @var array $query
     */
    private $query;

    /**
     * sets default query, gets all images by descending timestamp order
     */
    public function __construct() {
        $this->setSpectrum();
        $this->setOrder();
        $this->setTags();
    }

    /**
     * sets the order of the search from the string $ordertype, defaulting
     * to a descending timestamp result setting $desc to false
     * will generate an ascending order instead
     *
     * @param string $ordertype
     * @param bool $desc
     */
    public function setOrder($orderType = "default", $desc = true) {
        switch(strtolower($orderType)){
            case 'random':
                $this->query['order'] = "ORDER BY RAND()";
                break;

            default:
            case 'time':
                $this->query['order'] = "ORDER BY time "
                    . ($desc ? "DESC" : "ASC");
                break;

            case 'value':
                $this->query['order'] = "ORDER BY value "
                    . ($desc ? "DESC" : "ASC");
                break;

            case 'tags':
                $this->query['order'] = "ORDER BY tagCount "
                    . ($desc ? "DESC" : "ASC");

                    
        }
    }

    /**
     * returns a mysqli_result from created query
     * @return mysqli_result
     */
    public function search() {

        $query = 
            "SELECT image.*, tags.tag, COUNT(tags.id) as tagCount \n"
            . "FROM images AS image \n"
            . "LEFT JOIN taglinks AS tagl ON image.id = tagl.obj_id \n"
            . "LEFT JOIN tags ON tags.id = tagl.tag_id \n"
            . ($this->query['tagWhere'] != "" ? "WHERE "
                . $this->query['tagWhere'] . "\n" : "")
            . "GROUP BY image.id " . "\n"
            . $this->query['order'] . " ". $this->query['limit'];
        trigger_error("SQL Query is <pre>$query</pre>", E_USER_NOTICE);
        return SQL::query($query);
    }



    /**
     * sets the offset and limit of the sql query
     * @param int $offset
     * @param int $count
     */
    public function setSpectrum($offset=0, $count=12) {
        $this->query['limit'] = "LIMIT $offset, $count";
    }

    /**
     * setTags sets the tags we want to limit our search with. $include is
     * either an array or a string containing tags we want in our search and
     * $exclude is an array or string containing tags we want to exclude
     *
     * example: setTags(array(cute, cuddly_bears), "belch_demons") will set our
     * search to return all rows with the tags 'cute' and 'cuddly_bears' but
     * will ignore all rows with the tag 'belch_demon'
     *
     * example: setTags(null, "nice_things") is a instruction to how you can't
     * have nice things
     * 
     * @param array|string $include
     * @param array|string $exclude
     */
    public function setTags($include = null, $exclude = null) {
        $this->query['tagWhere']  = "";

        // tags to include in our search
        if($include) {
            if(is_array($include)) {
                foreach($include as &$tag)
                    $tag =  "tags.tag LIKE '$tag'";
                $include = implode($include, " OR ");
            } else if(is_string($include))
                $include = "tags.tag LIKE '$include'";
            else
                trigger_error("\$included is not of a valid type!",
                    E_USER_ERROR);

            $this->query['tagWhere'] .= "($include) ";

        }

        // tags to exclude in our search
        if($exclude) {
            if(is_array($exclude)) {
                foreach($exclude as &$tag)
                    $tag = "tags.tag NOT LIKE '$tag'";
                $exclude = implode($exclude, " AND ");

            } else if(is_string($exclude))
                $exclude = "tags.tag NOT LIKE '$exclude'";
            else
                trigger_error("\$exclude is not of valid type!",
                    E_USER_ERROR);

            if($include)
                $this->query['tagWhere'] .= "AND ";
                
            /* we need to include a check for our tags.tag that is null,
             * because of sql not reporting null values as false, but null. */
            $this->query['tagWhere'] .= 
                "(tags.tag IS NULL OR ($exclude)) ";

        }
    }

}
?>
