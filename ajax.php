<?php

require_once("./lib/Tags.php");
require_once("./lib/Upload.php");
// we really don't want to have errors showing up here
if(!isset($_GET['debug']) && $_GET['debug'] != true)
	error_reporting(!E_ALL);




switch ($_GET['do']) {
	/**
	 * Get the tags of an image
	 * 
	 * @param image valid image id
	 */
	case 'getTags':
		if(!isset($_GET['image'])) {
			echo "-10 Image is not set!";
			break;
		}
			
		if(!is_numeric($_GET['image'])){
			echo "-11 Image ID given is not a number!";
			break;
		}			
		
		$t = new Tags();
		echo json_encode($t->getImageTags($_GET['image']));
		break;
	
	/**
	 * Add tags to image
	 * 
	 * @param tags commaseparated string of tags
	 * @param image valid image id 
	 */
	case 'addTags':
		if(!isset($_GET['tags']) || !isset($_GET['image'])) {
			echo "-20 Tags or image where not given";
			break;
		}

		if(!is_numeric($_GET['image'])) {
			echo "-21 Image ID ".$_GET['image']." is not a number!";
			break;
		}			
			
			
		$t = new Tags();
		echo json_encode($t->get($t->connect((int)$_GET['image'], $_GET['tags'])));	
		
		break;
	
		
	/**
	 * Upload a file by URL
	 * 
	 * @param url url to upload to imagescript
	 */
	case 'uploadByURL':
		if(!isset($_GET['url'])){
			echo "-20 URL not set!";
			break;
		}
		
		$upload = new Upload();
		echo $upload->byURL($_GET['url']);
		
		break;
	
	case 'getImage':
		if(!isset($_GET['image']) || !is_numeric($_GET['image'])) {
			echo "-10 Image id not valid";
			break;
		}
		
		$result = SQL::Query("SELECT * FROM images" 
			. " WHERE id = '".$_GET['image']."' LIMIT 1");
		
			
		if($result->num_rows == 0) {
			echo "-11 No image at this ID!";
			break;
		}

		
		echo json_encode($result->fetch_assoc());
			
			
		break;
	default: 
		echo "-10 Operation not valid!";
		
		
		
}

?>