<?php


require_once("./config.php");
/**
 * the maximum width of a thumbnail
 * @var constant ISCRIPT_THUMBNAIL_MAXWIDTH
 */

if(!defined("THUMBNAIL_MAXWIDTH"))
    define("THUMBNAIL_MAXWIDTH", 150);

/**
 * Handles manipulation of image element
 * @package i
 */
class ImageManipulation {
    /**
     * the gd2 image resource we'll be manipulating
     * @var gd2resource $image
     * @access private
     */
    private $image;

    /**
     * the mime type of the image
     * @var string $mime
     * @access private
     */
    private $mime;

    /**
     * true|false if an image is loaded
     * @var bool $loaded
     * @access private
     */
    private $loaded;

    /**
     * checks mime type of file
     * @param string $file
     * @return bool
     * @access private
     */
    private function getMime($file){
	    $mime = null;
	    if(function_exists("mime_content_type")){
	        $mime = mime_content_type($file);
	    }
        else if(function_exists("finfo_open")) {
	        $finfo = finfo_open(FILEINFO_MIME);
	        $mime = finfo_file($finfo, $file);
	        finfo_close($finfo);
	    }
        else {
	        $fp = fopen($file, 'r');
	        $str = fread($fp, 4);
	        fclose($fp);
	        switch($str) {
	            case "\xFF\xD8\xFF\xE0":
	                $mime = 'image/jpeg';
	                break;
	            case "\x89PNG":
	                $mime = 'image/png';
	                break;
	            case 'GIF8':
	                $mime = 'image/gif';
	                break;
	        }
	    }
	    return $mime;
    }

    /**
     * sets default settings and loads an image if set
     * @param string $image
     */
    public function __construct($image = null) {
        $this->clear();
        if($image != null)
            $this->load($image);
    }

    /**
     * clears all settings
     */
    public function __destruct() {
        $this->clear();
    }

    /**
     * clears eventual loaded data
     */
    public function clear() {
        $this->image = null;
        $this->mime = null;
        $this->loaded = false;
    }

    /**
     * loads an image from string into the class
     * @param string $image
     * @return bool
     */
    public function load($image) {
        if($this->loaded == true) {
            trigger_error("Image has already been loaded, overwriting current"
                ." resource...", E_USER_NOTICE);
            $this->clear();
        }

        $this->mime = $this->getMime($image);

        switch($this->mime) {
            case 'image/jpeg':
                $this->image = imagecreatefromjpeg($image);
                break;
            case 'image/png':
                $this->image = imagecreatefrompng($image);
                break;
            case 'image/gif':
                $this->image = imagecreatefromgif($image);
                break;
            default:
                trigger_error("Could not create image! Image format not"
                    . " supported!", E_USER_WARNING);
                return false;
        }

        $this->loaded = true;
        return true;
    }


    /**
     * makes the loaded image into a thumbnail
     * @return bool
     */
    public function thumbnail() {
        if($this->loaded == false) {
            trigger_error("No image has been loaded yet!", E_USER_ERROR);
            return false;
        }

        $x = imagesx($this->image);
        $y = imagesy($this->image);

        // gets new dimensions of image
        if($x >= $y && $x > THUMBNAIL_MAXWIDTH) {
            $y = round($y/$x * THUMBNAIL_MAXWIDTH);
            $x = THUMBNAIL_MAXWIDTH;
        }
        else if($y > THUMBNAIL_MAXWIDTH) {
            $x = round($x/$y * THUMBNAIL_MAXWIDTH);
            $y = THUMBNAIL_MAXWIDTH;
        }

        // checks if the image is larger than THUMBNAIL_MAXWIDTH and then
        // resizes it
        if(imagesx($this->image) > THUMBNAIL_MAXWIDTH ||
            imagesy($this->image) > THUMBNAIL_MAXWIDTH) {
            $img = imagecreatetruecolor($x, $y);
            imagecopyresampled($img, $this->image, 0, 0, 0, 0,
                $x, $y, imagesx($this->image), imagesy($this->image));
            imagedestroy($this->image);
            $this->image = $img;
        }

        return true;
    }

    /**
     * saves the image to $output
     * @param string $output
     */
    public function save($output) {
        if(file_exists($output)){
            trigger_error("File already exists, can't save here!",
                E_USER_ERROR);
            return false;
        }

        imagepng($this->image, $output);
        return true;
    }
}
?>
