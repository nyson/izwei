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
 * SQL bindings for i
 */
require_once("./lib/SQL.php");

/**
 * Search handler for i
 * @package i
 */
class Search {

	/**
     * associative array of the objects in the row
     * @var array $query
     */
    private $query;
   

    /**
     * sets default query, gets all images by descending timestamp order
     */
    public function __construct() {
        $this->range();
        $this->order();
    }

    /**
     * order takes an array of constants and generates an advanced sql order
     * clause from it. More elements in the array means it sorts on more levels,
     * each sorting method given priority by its place in the array. Trying to
     * set the same type of value several times will result in a warning and
     * the declaration will be ignored.
     *
     * @param int ... the order of our search
     */
    public function order() {
		$order = array();
		$sort = func_get_args();
        $dualCheck = array("time" => false, "tagCount" => false, 
            "value" => false, "random" => false);

		if(func_num_args() == 1)
			if(is_array($sort[0]))
				$sort = $sort[0];
		else if(func_num_args() == 0)
			$sort = array(0);

        foreach($sort as $s){
        	switch($s){
                // sort newest first
                case SORT_NEWEST:
                    if($dualCheck['time']) {
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = SORT_NEWEST;
                    $dualCheck['time'] = true;
                    break;

                // sort random
                case SORT_RANDOM:
                     if($dualCheck['random']) {
                        trigger_error(
                            "Trying to sort by random twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = SORT_RANDOM;
                    $dualCheck['random'] = true;
                    break;

                // sort oldest first
                case SORT_OLDEST:
                    if($dualCheck['time']){
                        trigger_error(
                            "Trying to sort value twice! "
                            . "Declaration ignored!", E_USER_WARNING);
                        break;
                    }
                    $order[] = SORT_OLDEST;
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
                    $order[] = SORT_POPULARITY;
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
                    $order[] = SORT_IMPOPULARITY;
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
                    $order[] = SORT_LESSTAGS;
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
                    $order[] = SORT_MORETAGS;
                    $dualCheck['tagCount'] = true;
                    break;
                default: 
                	trigger_error("Sort mode not supported!", E_USER_NOTICE);
        		}
        		
            } // end of while
            
            
			if(count($order) == 0){
				trigger_error("No order given! Defaulting to SORT_NEWEST", E_USER_NOTICE);
	            $this->query['order'] = array(SORT_NEWEST);
			}
			else
	            $this->query['order'] = $order;
    }

    /**
     * Sets the range of our search. 
     * @param int $offset The offset to our search
     * @param int $count The maximum of returned objects
     */
    public function range($offset=0, $count = DEFAULT_IMAGE_COUNT) {
        $this->query['limit']['offset'] = $offset;
        $this->query['limit']['count'] = $count;
    }

    
    /**
     * Sets the tags we'll include in our search. No params will
     * unset any earlier given tags
     * @param string ...
     */
	public function with() {
		if(func_num_args() == 0) {
			trigger_error("No arguments given! Unsetting appropriate variables...", 
			E_USER_NOTICE);
			
			if(isset($this->query['include']))
                unset($this->query['include']);
            return;
        } 
		
		$input = func_get_args();
		if(func_num_args() == 1)
			if(is_array($input[0]))
				$input = $input[0];

        $include = array();
		// this traverses through input and generates a clean include array
		foreach($input as &$tag){
			if(count($tags = explode(',', $tag)) > 1) {
				while($t = array_pop($tags))
					$include[] = $t;
			}
            else
                $include[] = $tag;
        }

        $this->query['include'] = $include;	
	}

    /**
     * Sets the tags we'll exclude in our search. No params will
     * unset any earlier given tags.
     * @param string ...
     */	
	public function without(){
		if(func_num_args() == 0) {
			trigger_error("No arguments given! Unsetting appropriate variables...",
			E_USER_NOTICE);

			if(isset($this->query['exclude']))
                unset($this->query['exclude']);
            return;
        }

		$input = func_get_args();
		if(func_num_args() == 1)
			if(is_array($input[0]))
				$input = $input[0];
                
        $exclude = array();
		// this traverses through input and generates a clean include array
		foreach($input as &$tag){
			if(count($tags = explode(',', $tag)) > 1) {
				while($t = array_pop($tags))
					$exclude[] = $t;
			}
            else
                $exclude[] = $tag;
        }

        $this->query['exclude'] = $exclude;
    }

    /**
     * retrieves the tags as an sql query
     * @return string|bool a query on success or false
     */
    private function getTags() {
        $query = "";
        
        // including or excluding tags
        $include = isset($this->query['include'])
            && is_array($this->query['include']);
        $exclude = isset($this->query['exclude'])
            && is_array($this->query['exclude']);

        if($include || $exclude) {
            if($include) {
                $includeTags = $this->query['include'];
                foreach($includeTags as &$tag)
                    $tag = "tags.tag LIKE '$tag'";

                $query .= "(".implode($includeTags, " OR "). ")\n";
            }

            if($include && $exclude)
                $query .= "AND ";

            if($exclude) {
                $excludeTags = $this->query['exclude'];
                foreach($excludeTags as &$tag)
                    $tag = "tags.tag NOT LIKE '$tag'";

                $query .= "(".implode($excludeTags, " AND ").")\n";

            }
            return $query;
        }
            else return "TRUE ";
        
    }


    /**
     * retrieves the order of the search as an sql query
     * @return string|bool a query on success or false
     */
    private function getOrder($upward = true) {

        if(isset($this->query['order'])) {
            $query = array();
            foreach($this->query['order'] as $order) {
                switch($order) {
                    case SORT_NEWEST:
                        $query[] = "image.time "
                            . ($upward ? "DESC" : "ASC");
                        break;
                    case SORT_RANDOM:
                        $query[] = "RAND()";
                        break;
                    case SORT_OLDEST:
                        $query[] = "image.time "
                            . ($upward ? "ASC" : "DESC");
                        break;
                    case SORT_POPULARITY:
                        $query[] = "image.value "
                            . ($upward ? "DESC" : "ASC");
                        break;
                    case SORT_IMPOPULARITY:
                        $query[] = "image.value ASC"
                            . ($upward ? "ASC" : "DESC");
                        break;
                    case SORT_LESSTAGS:
                        $query[] = "tagCount "
                            . ($upward ? "DESC" : "ASC");
                        break;
                    case SORT_MORETAGS:
                        $query[] = "tagCount ASC"
                            . ($upward ? "ASC" : "DESC");
                        break;
                }

            }

            return "ORDER BY " . implode($query, " AND ") . "\n";
        }
            return false;
    }



    /**
     * returns a mysqli_result from created query
     * @return mysqli_result
     */
    public function search() {
        $query = "SELECT image.*, tags.tag, COUNT(tags.id) as tagCount \n"
            . "FROM images AS image \n"
            . "LEFT JOIN taglinks AS tagl ON image.id = tagl.object \n"
            . "LEFT JOIN tags ON tags.id = tagl.tag \n";

        $query .= "WHERE " . $this->getTags();
        $query .= "GROUP BY image.id " . "\n";
        $query .= $this->getOrder();
        $query .= "LIMIT ".$this->query['limit']['offset'].","
            .$this->query['limit']['count'];


        trigger_error("SQL Query is <pre>$query</pre>", E_USER_NOTICE);
        return SQL::query($query);
    }


    /**
     * retrieves the next image in the current search
     * @return mysqli_object
     */
    public function next($id) {
        $query = "SELECT image.*, tags.tag, COUNT(tags.id) as tagCount \n"
            . "FROM images AS image \n"
            . "LEFT JOIN taglinks AS tagl ON image.id = tagl.object \n"
            . "LEFT JOIN tags ON tags.id = tagl.tag \n";

        $currentImage = SQL::query($query
            . "WHERE image.id = '$id' GROUP BY image.id LIMIT 0,1")->fetch_object();

        $query .= "WHERE ".$this->getTags();
        $query .= "AND ". $this->getOrderAsWhereClause($currentImage);
        $query .= "AND image.id != '$currentImage->id' \n";


        $query .= "GROUP BY image.id " . "\n";
        $query .= $this->getOrder(true);
        $query .= "LIMIT ".$this->query['limit']['offset'].","
            .$this->query['limit']['count'];


        trigger_error("SQL Query is <pre>$query</pre>", E_USER_NOTICE);
        return SQL::query($query);
    }

    /**
     * Creates a where clause 
     * @param <type> $image
     * @return <type>
     */

    private function getOrderAsWhereClause($image, $increment = true) {

        if(isset($this->query['order'])) {
            $query = array();
            if($increment)
                foreach($this->query['order'] as $order) {
                    switch($order) {
                        case SORT_NEWEST:
                            $query[] = "image.time <= '$image->time'";
                            break;
                        case SORT_OLDEST:
                            $query[] = "image.time >= $image->time'";
                            break;
                        case SORT_POPULARITY:
                            $query[] = "image.value <= $image->value'";
                            break;
                        case SORT_IMPOPULARITY:
                            $query[] = "image.value >= $image->value'";
                            break;
                        case SORT_LESSTAGS:
                            $query[] = "tagCount >= $image->tagCount'";
                            break;
                        case SORT_MORETAGS:
                            $query[] = "tagCount <= $image->tagCount'";
                            break;
                    }

                }
            else
                foreach($this->query['order'] as $order) {
                    switch($order) {
                        case SORT_NEWEST:
                            $query[] = "image.time >= '$image->time'";
                            break;
                        case SORT_OLDEST:
                            $query[] = "image.time <= $image->time'";
                            break;
                        case SORT_POPULARITY:
                            $query[] = "image.value >= $image->value'";
                            break;
                        case SORT_IMPOPULARITY:
                            $query[] = "image.value <= $image->value'";
                            break;
                        case SORT_LESSTAGS:
                            $query[] = "tagCount <= $image->tagCount'";
                            break;
                        case SORT_MORETAGS:
                            $query[] = "tagCount >= $image->tagCount'";
                            break;
                    }

                }


            return "(" . implode($query, " AND ") . ")\n";
        }
            return "TRUE ";
    }

}
?>
