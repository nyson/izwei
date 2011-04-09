<?php

require_once("./lib/Tags.php");
require_once("./lib/Upload.php");
require_once("./lib/Search.php");
// we really don't want to have errors showing up here
if(!isset($_GET['debug']))
	error_reporting(!E_ALL);
else {
	error_reporting(E_ALL);
}

function axSetSearch(Search $search) {

    // gets the range of our search
    if(isset($_GET['offset'])) {
        if(isset($_GET['count']))
            $search->range($_GET['offset'], $_GET['count']);
        else
            $search->range($_GET['offset']);
    }
    else if(isset($_GET['count']))
        $search->range(NULL, $_GET['count']);

    // sets our tags
    if(isset($_GET['include'])) {
        $tags = explode(',', $_GET['include']);
        foreach($tags as $k => $v) {
            $tags[$k] = urldecode($v);
        }
        $search->with($tags);
    }

    if(isset($_GET['exclude'])) {
        $tags = explode(',', $_GET['exclude']);
        foreach($tags as $k => $v) {
            $tags[$k] = urldecode($v);
        }
        $search->without($tags);
    }

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
                case 'mosttags':
                	$o = SORT_MORETAGS;
                	break;
                case 'leasttags':
                	$o = SORT_LESSTAGS;
                	break;
                default:
                    trigger_error("Sort mode $o is not supported!",
                        E_USER_NOTICE);
            }
        $search->order($orders);
    }

    return $search;
}



/****************** * * * START OF $_GET['do'] SWITCH! * * * ******************/
switch ($_GET['do']) {
	/**
	 *  This will fetch a full list of images
	 */
	case 'getImages': 
		$s = new Search();

        $s = axSetSearch($s);

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
    /**
     * This will call forth the next image by the given search and id
     * needs $_GET param 'id' for the image we have right now
     */
    case 'nextImage':
        if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            echo "-60 No or invalid id!";
            break;
        }

        $s = new Search();

        // retrieves our search based on get values
        $s = axSetSearch($s);

        $r = $s->next($_GET['id']);

        if(!$r) {
            echo "-61 current id not found in database!";
            break;
        } else if($r->num_rows === 0){
            echo "-62 next image could not be found!";
            break;
        }
        
        echo json_encode($r->fetch_object());

        break;

    /**
     * default behaviour is telling us that we need to bring a valid
     * do-operand to get valid results 
     */
	default: 
		if(isset($_GET['do']))
			echo "-10 Operation '".$_GET['do']."' not valid!";
		else
			echo "-11 No operation set!";
		
		
		
}

?>
