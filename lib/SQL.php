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

        if(!self::$session) {
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
     * submits an sql query
     * 
     * @param string $query the SQL query we entered
     */
    public static function query($query) {
        if(!self::isOpen())
            self::connect();
        $res = self::$session->query($query)
            or die("<p>Malformed query: '$query' : "
                . self::$session->error."</p>");
        return $res;
    }

    public static function lastId() {
    	return self::$session->insert_id;
    }
    
    public static function escape($string) {
    	return self::$session->real_escape_string($string);
    }
}

?>
