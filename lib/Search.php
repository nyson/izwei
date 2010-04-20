<?php

/**
 * manages functions of getting images
 * @package i
 */


/**
 * avaliable sort methods
 */
define("SORT_RANDOM", 0);
define("SORT_NEWEST", 1);
define("SORT_OLDEST", 2);
define("SORT_POPULARITY", 3);
define("SORT_IMPOPULARITY", 4);
define("SORT_MORETAGS", 5);
define("SORT_LESSTAGS", 6);

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
     * setOrder takes an array or string and generates an advanced sql order
     * clause from it. More elements in the array means it sorts on more levels,
     * each sorting method given priority by it's place in the array. Trying to
     * set the same type of value several times will result in a warning and
     * the declaration will be ignored.
     *
     * avaliable methods:
     *  * newest: sort newest first
     *  * oldest: sort oldest first
     *  * popularity: sort images with highest value first
     *  * impopularty: sort images with least value first
     *  * lesstags: images with least tags first
     *  * moretags: images with more tags first
     *
     * @param string $ordertype
     * @param bool $desc
     */
    public function setOrder($sort = null) {
        $order = array();
        $dualCheck = array("time" => false, "tagCount" => false, 
            "value" => false, "random" => false);


        while($s = is_array($sort) ? array_shift($sort) : $sort)
            switch(strtolower($s)){
                // sort random
                case SORT_RANDOM:
                     if($dualCheck['random']) {
                        trigger_error(
                            "Trying to sort by random twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = "RAND()";
                    $dualCheck['random'] = true;
                    break;

                // sort newest first
                default:
                case SORT_NEWEST:
                    if($dualCheck['time']) {
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = "image.time DESC";
                    $dualCheck['time'] = true;
                    break;

                // sort oldest first
                case SORT_OLDEST:
                    if($dualCheck['time']){
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = "image.time ASC";
                    $dualCheck['time'] = true;
                    break;

                // sort best voted first
                case SORT_POPULARITY:
                    if($dualCheck['value']) {
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = "image.value DESC";
                    $dualCheck['value'] = true;
                    break;

                // sort worst voted first
                case SORT_IMPOPULARITY:
                    if($dualCheck['value']){
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = "image.value ASC";
                    $dualCheck['value'] = true;
                    break;

                // sort images with least tags first
                case SORT_LESSTAGS:
                    if($dualCheck['tagCount']) {
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = "tagCount ASC";
                    $dualCheck['tagCount'] = true;
                    break;

                //  sort images with most tags first
                case SORT_MORETAGS:
                    if($dualCheck['tagCount']) {
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = "tagCount DESC";
                    $dualCheck['tagCount'] = true;
                    break;
            }

            $this->query['order'] = "ORDER BY " . implode($order, ", ");
    }

    /**
     * returns a mysqli_result from created query
     * @return mysqli_result
     */
    public function search() {

        $query = "SELECT image.*, tags.tag, COUNT(tags.id) as tagCount \n"
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
