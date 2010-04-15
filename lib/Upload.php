<?php

require_once("./lib/SQL.php");
require_once("./config.php");
require_once("./lib/ImageManipulation.php");


if(!defined("I_THUMBNAIL_DIR"))
    define("I_THUMBNAIL_DIR", "./thumbs");
if(!defined("I_IMAGE_DIR"))
    define("I_IMAGE_DIR", "./images");

/**
 * Upload functionality for i
 *
 * @package i
 */
class Upload {
    /**
     * an array containing the result from $_FILES
     * @var array $file
     * @access private
     */
    private $file;

    public function listen() {
        if(isset($_FILES['uploadImage'])){
            $this->file = $_FILES['uploadImage'];

            if($this->file['size'] > MAX_FILE_SIZE)
                trigger_error("Image too large!", E_USER_NOTICE);
            else
                trigger_error("Image size is OK!", E_USER_NOTICE);

            $this->file['hash'] = hash_file("md5", $this->file['tmp_name']);
            trigger_error("File hash is: ".$this->file['hash'], E_USER_NOTICE);

            $res = SQL::query("SELECT COUNT(*) as images FROM images"
                . " WHERE hash = '".$this->file['hash']."' LIMIT 1");

            // checks if image is new and if not only grants the image a new
            // timestamp
            if($res->fetch_object()->images > 0){
                trigger_error("Hash is not unique, will only update image",
                    E_USER_NOTICE);
                SQL::query("UPDATE images SET images.time = '".time()."' "
                    ."WHERE hash = '" .$this->file['hash']. "' LIMIT 1");
            }

            // a whole new image! yayers!
            else {
                trigger_error("Image is unique, will create new image!",
                    E_USER_NOTICE);

                $manip = new ImageManipulation($this->file['tmp_name']);
                $manip->thumbnail();
                trigger_error("Saving thumbnail to ".I_THUMBNAIL_DIR.DIRECTORY_SEPARATOR
                    . $this->file['name'],E_USER_NOTICE);

                $this->cleanFilename();

                if(!copy($this->file['tmp_name'], I_IMAGE_DIR 
                    . DIRECTORY_SEPARATOR . $this->file['safeName']))
                    trigger_error("Couldn't copy file to destination!",
                        E_USER_ERROR);
                
                $manip->save(I_THUMBNAIL_DIR
                        . DIRECTORY_SEPARATOR . $this->file['safeName']);
                unset($manip);
                
                $this->insertIntoDB();
            }

            
        }
    }

    private function cleanFilename() {
        $name = $this->file['name'];
        if($name === "" || $name == null)
            trigger_error("Filename is empty!", E_USER_ERROR);

        // removes illegal characters
        $badChars = 
            array('/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', ',');
        $name = str_ireplace($badChars, "", $name);

        // if the whole string concisted of illegals, randomize new name
        if($name == "")
            $name = substr(md5(uniqid().rand()), 0, 5);

        while(file_exists(I_IMAGE_DIR . DIRECTORY_SEPARATOR . $name)
            || file_exists(I_THUMBNAIL_DIR . DIRECTORY_SEPARATOR . $name))
            $name = substr(md5(uniqid().rand()), 0, 5) . $name;

        $this->file['safeName'] = $name;
    }


    private function insertIntoDB() {
        trigger_error("Inserting file into database...", E_USER_NOTICE);
        $width = getimagesize($this->file['tmp_name']);
        $width = $width[0];

        SQL::query("INSERT INTO images (file,hash,time,name,ip,width)"
            . "VALUES ("
            . "'" . mysqli_escape_string(SQL::$session, $this->file['safeName'])
            . "', '" . mysqli_escape_string(SQL::$session, $this->file['hash'])
            . "', '" . time()
            . "', '" . mysqli_escape_string(SQL::$session, $this->file['name'])
            . "', '" . $_SERVER['REMOTE_ADDR']
            . "', '$width')");
    }
    
}
?>
