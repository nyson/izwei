<?php 

if(!isset($_GET['debug'])) {
    header("Content-Type: application/rss+xml");
    error_reporting(!E_ALL);
}
define("BASE_URL", "http://jont.se/~nyson/i/");
require_once("lib/rss/RSSBuilder.php");
require_once("lib/Search.php");

$rss = new RSSBuilder();
$s = new Search();
$maxTime = 0;
$result = $s->search();

while ($row = $result->fetch_object()) {
	$link = BASE_URL . "#$row->id";
	$item = new RSSBuilderItem();

	$item->addTag("title", "Wohoo, someone uploaded $row->name!");
	$item->addTag("guid", $link, array("isPermaLink" => "false"));
	$item->addTag("link", $link);	
	$item->addTag('pubDate', RSSBuilder::formatTimestamp($row->time));

	if($row->time > $maxTime) $maxTime = $row->time;

	$item->addTag("description", 
	    htmlentities("<div>"
	        . "<h2>$row->name</h2>"
            . "<div style='float: left;'><a href='$link' title='$row->name'>"
                . "<img src='".BASE_URL."thumbs/$row->file' alt='$row->name' />"
            ."</a></div>"
            ."</div>"));


	
	$rss->addItem($item);	
}

$rss->setInfo(array("lastBuildDate" => RSSBuilder::formatTimestamp($maxTime)));
if(!isset($_GET['debug']))
    echo $rss->build();
else    
    echo str_replace(
        array("\n", "\t"), 
        array("<br />", "&nbsp;&nbsp;&nbsp;&nbsp;"),htmlentities($rss->build()));
?>
