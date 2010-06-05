<?php
/**
 * SQL manager for i
 * @package i
 */

/**
 * we require a config file!
 */
require_once("./config.php");

/**
 * SQL class for i
 * @package i
 */
class SQL {
    private static $open;
    public static $session;

    /**
     * checks if the connection to the database is open
     * 
     * @return bool returns true if connection is open, else false
     */
    public static function isOpen(){
        if(!isset(self::$open) || !self::$open)
            return false;
        return true;
    }

    /**
     * connects to the selected database and server selected in ./config.php
     *
     * @return bool true on success, else false and triggers an error
     */
    public static function connect(){
        if(self::isOpen())
            return true;

        self::$session = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD,
            MYSQL_DATABASE);
		
        if(!self::$session || mysqli_connect_errno()) {
            trigger_error("SQL Session couldn't be opened: " .  self::$session->error
                , E_USER_ERROR);
            return false;
        }

        self::$open = true;
        return true;
    }

    /**
     * disconnects from $session. returns true on success or false and triggers
     * an error.
     *
     * @return bool true on disconnect, else false
     */
    public static function disconnect() {
        if(!self::isOpen()) {
            trigger_error("SQL Session can't close! It's not open!",
                E_USER_NOTICE);
            return false;
        }

        if(!self::$session->close()){
            trigger_error("SQL Session could not be closed: " 
                . self::$session->error, E_USER_ERROR);
            return false;
        }

        self::$session = null;
        self::$open = false;
        return true;
    }
	

    /**
     * submits a sql query
     * 
     * @param string $query the SQL query we entered
     */
    public static function query($query) {
    	if(!isset($query))
    		trigger_error("No query is set!", E_USER_ERROR);
        if(!self::isOpen())
            self::connect();
        $res = self::$session->query($query);
        if(self::$session->error)
			die("<p>Malformed query:<br /> <pre>'$query'</pre> : "
                . self::$session->error."</p>");
        return $res;
    }
	/**
	 * returns the last primary key of an insert query
	 * if last operation wasn't an insert operation, it will trigger an error
	 * 
	 * @return the id of last SQL INSERT operation
	 */
    public static function insertId() {
    	if(self::$session->insert_id == 0)
    		trigger_error("Last operation wasn't an insert operation! "
    			. "Can't fetch last inserted ID!",
    			E_USER_ERROR);
    	return self::$session->insert_id;
    }
    
    /**
     * 
     * @param string $string 
     */
    public static function escape($string) {
    	if(!is_string($string))
    		trigger_error("$string isn't really a string, it doesn't need to be escaped",
    			E_USER_NOTICE);
    	return self::$session->real_escape_string($string);
    }
}

?>
