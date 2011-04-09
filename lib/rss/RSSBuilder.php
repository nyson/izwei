<?php

require_once("./lib/rss/RSSBuilderItem.php");
require_once("./lib/rss/RSSBuilderTag.php");

/**
 * Class for building rss feeds and validate them in a simple manner
 *
 * @package i
 */
class RSSBuilder {
	private $items;
	private $channelinfo;
	
	/**
  	 * static function for generating timestamps in a simple manner
	 *
	 * @param $timestamp
	 */
	public static function formatTimestamp ($timestamp) {
		return date(DATE_RSS, $timestamp);
	}
	
	/**
	 *
	 */
	public function __construct() {
		
		$this->items = array();
		$this->channelinfo = array(
			"title" => "i^2 image feed",
			"link" 	=> "http://jont.se/~nyson/i",
			"description" => "images lol",
			"atom:link" => new RSSBuilderTag("atom:link", "", 
			    array("href" => "http://jont.se/~nyson/i/rss.php", 
			        "rel" => "self", "type" => "application/rss+xml")
	        )
		);

	}
	
	/** 
	 * Sets the tags inside the RSS <channel> tag
	 * @param $arr key/value array representation of tag given <key>value</key>
	 */
	public function setInfo($arr) {
		$this->channelinfo = array_merge($this->channelinfo, $arr);
	}
	
	/** 
	 * Adds a new item to the build que
	 * @param $rssItem RSSBuilderItem object to prepend to the que
	 */
	public function addItem($rssItem) {
		$this->items[] = $rssItem;
	}

	/**
	 * Builds the feed with the given parameters
	 * 
	 */
	public function build() {
		$out = '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
		$out .= "\n\t<channel>";
		foreach ($this->channelinfo as $tag => $info)
		    if($info instanceof RSSBuilderTag){
		        $out .= "\n\t" . $info->asString();
		    } else {
		        switch($tag) {
		            default:
        			    $out .= "\n\t\t<$tag>$info</$tag>";
        			    break;
			        case "guid": 
			            $out .= "\n\t\t<$tag isPermaLink='false'>$info</$tag>";
			    }
		    }
		
		foreach($this->items as $item)
			$out .= $item->getAsFeedItem();
		
		$out .= "\n\t</channel>\n</rss>\n";
		
		return $out;	
	}
}


?>
