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
                    $order[] = "image.time DESC";
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
                    $order[] = "RAND()";
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
                default: 
                	trigger_error("Sort mode not supported!", E_USER_NOTICE);
        		}
        		
            } // end of while
            
            
			if(count($order) == 0){
				trigger_error("Orders ignored!", E_USER_NOTICE);
	            $this->query['order'] = "ORDER BY image.time DESC";
			}
			else
	            $this->query['order'] = "ORDER BY " . implode($order, ", ");
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
            
        if(isset($this->query['include']) || isset($this->query['exclude'])) {
        	$query .= "WHERE ";
        	if(isset($this->query['include']) && isset($this->query['exclude']))
                $query .= $this->query['include'] . "AND " . $this->query['exclude'];
        	else
        	    $query .= isset($this->query['include'])
        		  ? $this->query['include'] : $this->query['exclude'];
            $query .= "\n";
        }        
            
        $query .= "GROUP BY image.id " . "\n"
            . $this->query['order'] . " ". $this->query['limit'];
            
        trigger_error("SQL Query is <pre>$query</pre>", E_USER_NOTICE);
        return SQL::query($query);
    }



    /**
     * Sets the range of our search. 
     * @param int $offset The offset to our search
     * @param int $count The maximum of returned objects
     */
    public function range($offset=0, $count=12) {
    	if(!isset($offset))
    		$offset = 0;
    	if(!isset($count))
    		$count = DEFAULT_IMAGE_COUNT;
    		
        $this->query['limit'] = "LIMIT $offset, $count";
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
		
		$include = func_get_args();
		if(func_num_args() == 1)
			if(is_array($include[0]))
				$include = $include[0];
		
		foreach($include as &$tag){
			if(count($tags = explode(',', $tag)) > 1) {
				while($t = array_pop($tags))
					$include[] = $t;
			} else
				$tag = "tags.tag LIKE '$tag'";
		}
        $this->query['include'] = "(". implode($include, " OR ") . ") ";
		
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
		$exclude = func_get_args();
		if(func_num_args() == 1)
			if(is_array($exclude[0]))
				$exclude = $exclude[0];
				
		foreach($exclude as &$tag)
            $tag = "tags.tag NOT LIKE '$tag'";
		$this->query['exclude'] = "(tags.tag IS NULL OR ("
		  . implode($exclude, " AND ") . ")) ";
	}	
}
?>
