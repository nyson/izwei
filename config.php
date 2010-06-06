<?
/**
 * Configuration file for izwei
 *
 * @package i
 */

// Database
define("MYSQL_SERVER", "localhost");
define("MYSQL_USERNAME", "i");
define("MYSQL_PASSWORD", "i");
define("MYSQL_DATABASE", "i");

// Internal variables
// the maximum file size, in bytes
define("MAX_FILE_SIZE", 4*1024*1024);
define("DEFAULT_IMAGE_COUNT", 12);

// the timeout of dowloading images by cURL
define("DOWNLOAD_TIMEOUT", 60);
//define("THUMBNAIL_MAXWIDTH", 150);
//define("I_THUMBNAIL_DIR", "./images/");
//define("I_IMAGE_DIR", "./thumbnails/");

?>