<?php
/**
 * An Item handler for the RSSBuilder class
 *
 * @package i
 */
class RSSBuilderItem {
	private $item;
	private $tags;
	
	/** 
	 * Constructor
	 */
	public function __construct() {}
	
	/**
	 * Adds a tag to our RSS Item
	 * 
	 * @param $tag tag in string form
	 * @param $content content of the RSS tag
	 * @param $attr key/value-array of attributes
	 *
	 * @return bool true on succesful entry
	 */
	public function addTag($tag, $content, $attr = null) {
		$validTags = array("title", "link", "description", "pubDate", "author",
			"category", "comments", "enclosure", "guid", "source");	
		if(!in_array($tag, $validTags))
		    return false;
		    
	   	$this->tags[$tag] = new RSSBuilderTag($tag, $content, $attr);
	   	return true;
	}
	
	/**
	 * Builds the RSS Item and returns as a feed item string
	 * 
	 * @return the feed item or false if needed tags are missing
	 */
	public function getAsFeedItem() {
	    $foundTags = array();
	    foreach($this->tags as $t)
	        $foundTags[] = $t->name;
		if(!(in_array("link", $foundTags)
		    && in_array("description", $foundTags)
		    && in_array("title", $foundTags)
	    )){
			trigger_error("Missing parameters for building an RSS <item> object!",
				E_USER_ERROR);
			return false;
		}
			
		$out = "\n\t\t<item>";
		foreach($this->tags as $tag){
            $out .= "\n\t\t\t" . $tag->asString() . "";
		}
		$out .=  "\n\t\t</item>";
		
		return $out;
		
	}
	
	
}
?>
