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

END;
	}
	
	public function head() {
		echo <<<HEADEND

    <title>I it is...</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/dialogs.css" />
    <link rel="stylesheet" href="./css/menu.css" />
    <link rel="stylesheet" href="./css/content.css" />
    <link rel="stylesheet" href="./css/navigation.css" />
    <link rel="stylesheet" href="./css/image.css" />

    <script type="text/javascript" src="./js/jquery/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="./js/jquery/jquery-ui-1.8.1.custom.min.js"></script>
	<script type="text/javascript" src="./js/DOMNodes/searchNodes.js"></script>
    <script type="text/javascript" src="./js/base.js"></script>
    <script type="text/javascript" src="./js/base64.js"></script>
    <script type="text/javascript" src="./js/main.js"></script>
    <script type="text/javascript" src="./js/upload.js"></script>
    <script type="text/javascript" src="./js/dialogs.js"></script>
    <script type="text/javascript" src="./js/bindings.js"></script>
    <script type="text/javascript" src="./js/imageModal.js"></script>
    <script type="text/javascript" src="./js/search.js"></script>
    <script type="text/javascript" src="./js/listMenu.js"></script>
    
HEADEND;
	}
	
	public function footer() {
		echo "</html>";
	}
	
	public function menu() {
		echo <<<ENDMENU
    <div id="colMenu">
    	<h2>i&sup2;</h2>
    	
    	<br />
    	<div class="menuIcon">
        <div id="popupBubbleSearch" class="menuPopupContainer">
    	<div class="menuPopupWing"></div>
    		<div class="menuPopup"> 
				<h3>Search!</h3>
       			<p><a href='javascript:searchHelp();' title='HALP'>Help!</a>
				<input type="text" class="iWantFocus" id="quickSearchText" />
        	   	<input class="search" id='quickSearchExecute' type="button" value="Go!" />
	            </p><div id='searchRules'></div>	    		
	    	</div>
			</div>
			
	    	<a class="menuIconLink" href="javascript:bubble('search');" title="Search (s)">
	    		<img src="./design/icons/find.png" alt="Search (s)" />
	    	</a>
		</div>	    	
    	
    	<div class="menuIcon">
	        <div id="popupBubbleUpload" class="menuPopupContainer">
		    	<div class="menuPopupWing"></div>
	    		<div class="menuPopup">
					<form action="./" method="post" enctype="multipart/form-data">
	     			<h3>Upload by file...</h3>
	     			<p>
	                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE ?>" />
	                <input type="file" size='10' id='uploadImage' name="uploadImage" /><br />
	                <span>...or enter an url!</span><br />
	                <input type="text" class="iWantFocus" id="uploadURL" /> <br />
	                <label id='uploadLabel' class='statusMessage'></label>
	                <input type="submit" id="submitImage" onclick='return validateUpload();' name="submitImage" value="Go!" />
	                </p>
                	</form>
        		</div>
			</div>    	
    	
	    	<a class="menuIconLink" href="javascript:bubble('upload');" title="Upload (u)">
	    		<img src="./design/icons/image_add.png" alt="Upload (u)"  />
	    	</a>
    	</div>

    </div>		
ENDMENU;
	}
	
	public function content() {		
	    echo '<div id="thumbnails">';
	    
		// generate a basic newest search with a range from 0 to 12
	    $s = new Search();
        $s->order(SORT_NEWEST);
        $s->range(0, 12);
        $res = $s->search();

        while($image = $res->fetch_object())
        	echo $this->imageBlock($image);            


		echo '</div><div id="modal"></div><div id="modalContent"> </div>';		
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
                . " src='./thumbs/".htmlentities($image->file)."'"
                . " alt='" . htmlentities($image->name) . "' /></a>"
                . $this->imageOperations($image)
                . "</div>";		
	}
	
	public function imageOperations($image) {
		return "<div class='imageOperations'>"
			. "<div class='tagBlock'>"
			. "<a href='javascript:tagDialog($image->id);' title='Edit tags!'>"
			. "<img class='tagAction' src='./design/icons/tag.png' alt='Tag this image!' />" 
			. "<span>$image->tagCount</span></a></div>"
			. "</div>";
		
	}
		
	
}
?>
