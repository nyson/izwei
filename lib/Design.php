<?php
/**
 * Description of Design
 *
 * @package i
 */

/**
 *  Avaliable form types
 */
define("FORM_SIMPLESEARCH", 0);
define("FORM_UPLOAD", 1);
define("FORM_ADVANCEDSEARCH", 2);
define("FORM_ADDTAGS", 3);


/**
 * @package i
 */
class Design {
	public function __construct() {}

	
	public function header() {
		echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>I it is...</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="./css/form.css" />
    <link rel="stylesheet" href="./css/dialogs.css" />


    <script type="text/javascript" src="./js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="./js/jquery-ui-1.8.1.custom.min.js"></script>
    <script type="text/javascript" src="./js/main.js"></script>
    <script type="text/javascript" src="./js/design/dialogs.js"></script>
    <script type="text/javascript" src="./js/design/bindings.js"></script>
    <script type="text/javascript" src="./js/design/imageMonad.js"></script>
    <script type="text/javascript" src="./js/design/search.js"></script>
    
</head>
END;
	}
	
	public function footer() {
		echo "</html>";
	}
	
	public function form($type, $vars = null) {
		
		switch($type) {
			case FORM_SIMPLESEARCH:
                return
        "<fieldset id=\"searchField\"><legend>Search</legend>
            Enter a search term here... <br />
            <input type=\"text\" id=\"quickSearchText\" />
            <input class=\"search\" id='quickSearchExecute' type=\"button\" value=\"Go!\" />
            <br /><div id='searchRules'></div>
        </fieldset>";
                break;
				
            case FORM_UPLOAD:
            	return
        "<form action=\"./\" method=\"post\" enctype=\"multipart/form-data\">
            <fieldset id=\"uploadField\"><legend>Upload</legend>
                Upload by file...<br />
                <input type=\"hidden\" name=\"MAX_FILE_SIZE\"
                       value=\"". MAX_FILE_SIZE . "\" />
                <input type=\"file\" size='10' id='uploadImage' name=\"uploadImage\" /><br />
                ...or enter an url!<br />
                <input type=\"text\" id=\"uploadURL\" /> <br />
                <label id='uploadLabel' class='statusMessage'></label>
                <input type=\"submit\" onclick='return validateUpload();' name=\"submitImage\" value=\"Go!\" />
                
            </fieldset>
        </form>";           
            	break; 
            	
            case FORM_ADDTAGS:
                return 
        "<form action='./' method='post'>
        <fieldset id='addTagField'><legend>Add tag!</legend>
            <input type='text' name='tags' value='Add tags here!' />
			<input type='hidden' name='imageID' value='".$vars['imageID']."' />
            <input type='submit' value='Go!'>
		</fieldset>
        </form>";
                break;
		}
		
		  
	}
	/**
	 * Takes a result of mysqli-result::fetch_object();
	 * 
	 * @param stdClass $image
	 */
	public function imageBlock($image) {
		return "<div class='imageBlock'>"
                . "<a href='javascript:viewImage($image->id);' title='Click to zoom!'>"
                . "<img id='image$image->id' class='thumbnail'" 
                . " src='./thumbs/$image->file'"
                . " alt='$image->name' /></a>"
                . $this->imageOperations($image)
                . "</div>";		
	}
	
	public function imageOperations($image) {
		return "<div class='imageOperations'>"
			. "<a href='javascript:tagDialog($image->id);' title='Tag this image!'>"
			. "<img class='tagAction' src='./design/icons/tag.png' alt='Tag this image!' /> </a>"
			. "</div>";
		
	}
		
	
}
?>
