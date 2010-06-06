<?php

require_once("./lib/Tags.php");
require_once("./lib/Upload.php");
require_once("./lib/Search.php");
// we really don't want to have errors showing up here
if(!isset($_GET['debug']))
	error_reporting(!E_ALL);



switch ($_GET['do']) {
	/**
	 *  This will fetch a full list of images
	 */
	case getImages: 
		$s = new Search();
		
		// gets the range of our search
		if(isset($_GET['offset'])) {
			if(isset($_GET['count']))
				$s->range($_GET['offset'], $_GET['count']);
			else
				$s->range($_GET['offset']);
		}	
		else if(isset($_GET['count']))
			$s->range(NULL, $_GET['count']);

		// sets our tags
		if(isset($_GET['include']))
			$s->with(explode(',', $_GET['include']));
		if(isset($_GET['exclude']))
			$s->without(explode(',', $_GET['exclude']));
		
		// sets our order
		if(isset($_GET['order'])) {
			$orders = explode(',', $_GET['order']);
			
			foreach($orders as $key => &$o)
				switch($o) {
					case 'newest':
						$o = SORT_NEWEST;
						break;
					case 'oldest':
						$o = SORT_OLDEST;
						break;
					case 'best':
						$o = SORT_POPULARITY;
						break;
					case 'worst':
						$o = SORT_IMPOPULARITY;
						break;
					case 'random':
						$o = SORT_RANDOM;
						break;
					default: 
						trigger_error("Sort mode $o is not supported!", E_USER_NOTICE);
				}
			$s->order($orders);
		}

		$result = $s->search();
		$images = array();
		while($image = $result->fetch_assoc())
			$images[] = $image;
			
		echo json_encode($images);
		
		break;
	
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
