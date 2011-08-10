<?php

/**
 * upload functionality for i
 * @package i
 */


/**
 * file imports
 */
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
    
    public function byURL($url) {
    	if(!isset($url) || !is_string($url))
    		trigger_error("Invalid input!", E_USER_ERROR);

    	// check if the image is downloadable
    	$curlHandle = curl_init();
    	curl_setopt_array($curlHandle, array(
    		CURLOPT_URL => $url,
    		CURLOPT_NOBODY => true,
    		CURLOPT_HEADER => false
    	));
    	$res = curl_exec($curlHandle);
    	if(!$res)
    		return "-11\nFile doesn't exist!";

    	$i = curl_getinfo($curlHandle);
    	if($i['download_content_length'] > MAX_FILE_SIZE)
    		return "-12\nFile too large!";
    	
    	// Download the image!
    	$this->file['tmp_name'] = tempnam("/tmp", "i2");
    	$fileHandle = fopen($this->file['tmp_name'], 'w');
    	$curlHandle = curl_init();
    	curl_setopt_array($curlHandle, array(
    		CURLOPT_URL => $url,
    		CURLOPT_FILE => $fileHandle,
    		CURLOPT_CONNECTTIMEOUT => DOWNLOAD_TIMEOUT,
    		CURLOPT_BINARYTRANSFER => 1
    	));
    	
    	$download = curl_exec($curlHandle);
    	fclose($fileHandle);
    	
    	if(curl_errno($curlHandle) != 0)
    		return "-13\nInternal cURL error: (" . curl_errno($curlHandle) 
    			. ") " . curl_error($curlHandle);

    	$this->file['hash'] = hash_file("md5", $this->file['tmp_name']);
		
		// checks if image is new and if not only grants the image a new
		// timestamp
		$res = SQL::query("SELECT COUNT(*) as images FROM images"
			. " WHERE hash = '".$this->file['hash']."' LIMIT 1");
				
		if($res->fetch_object()->images > 0){
			
			trigger_error("Hash is not unique, will only update image",
				E_USER_NOTICE);
			SQL::query("UPDATE images SET images.time = '".time()."' "
				. "WHERE hash = '" .$this->file['hash']. "' LIMIT 1");

			
			return "2\nSuccess! Image updated in database!";
		}
		else {
			
	    	$this->file['name'] = explode('/', $url);
	    	$this->file['name'] = $this->file['name'][count($this->file['name']) - 1];
	    	$this->cleanFilename();
	    	
			if(!copy($this->file['tmp_name'], I_IMAGE_DIR 
            	. DIRECTORY_SEPARATOR . $this->file['safeName']))
                	return "-14\nCould not copy file to image directory";
            
			
			$manip = new ImageManipulation($this->file['tmp_name']);
			$manip->thumbnail();
			$manip->save(I_THUMBNAIL_DIR
                        . DIRECTORY_SEPARATOR . $this->file['safeName']);
			
			unset($manip);

			$this->insertIntoDB();
			
			return "1\nSuccess! '".$this->file['safeName']."' uploaded!";
		}
    	
    }

    
    private function cleanFilename() {
        $name = $this->file['name'];
        if($name === "" || $name == null)
            trigger_error("Filename is empty!", E_USER_ERROR);

        // removes illegal characters
        $badChars = array('/', '\\', '?', '%', '*', ':', '|', 
                '"', '<', '>', ',', "-", "_");
        $name = str_replace($badChars, "", $name);

        // if the whole string concisted of illegals, randomize new name
        while($name == "")
            $name = substr(md5(uniqid().rand()), 0, 5);



        if(preg_match("/.*\..+/i", $name) == 0) {
            switch(ImageManipulation::getMime($this->file['tmp_name'])) {
                case 'image/jpeg':
			    case 'image/jpeg; charset=binary':
                    $name .= ".jpg";
                    break;

                case 'image/png':
                case 'image/png; charset=binary':
                    $name .= ".png";
                    break;
                    
                case 'image/gif':
                case 'image/gif; charset=binary':                
                    $name .= ".gif";
                    break;
                
                default:
                    trigger_error("Unsupported mime type, I wont give $name a file extension!", 
                        E_USER_NOTICE);
            }
            
        }
        
        // fixes 
		
        $name = preg_replace("/(.*?)([!.]+)(\.)(jpg|gif|jpeg|png)(.*)/", "${2}.${4}", $name);        

        while(file_exists(I_IMAGE_DIR . DIRECTORY_SEPARATOR . $name)
            || file_exists(I_THUMBNAIL_DIR . DIRECTORY_SEPARATOR . $name)){
            $name = rand() %10 . $name;
        }

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
