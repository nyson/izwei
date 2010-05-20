<?php
/**
 * Handles tags
 * 
 * @package i
 */

require_once("./lib/SQL.php");


class Tags {
	/**
	 * Connects an image with one or more tags. 
	 * Tags will be added if not existant in database.
	 * 
	 * @param int $image id of the image
	 * @param string
	 * @return value of the tag added
	 */
	public function connect($object, $tags) {
		if(!is_int($object) || !(is_string($tags) || is_array($taggs))) 
			trigger_error("Parameters is of unvalid type(s)!", E_USER_ERROR);
		
		$tags = $this->clean($tags);
			
		if($keys = $this->add($tags))
			foreach($keys as $key) {
				$query = "INSERT INTO taglinks (image, tag, id)"
					.  " VALUES ($object, $key, '')";
				trigger_error("Linking query: '$query'", E_USER_NOTICE);
				SQL::query($query);					
			}

	}
	
	/**
	 * Adds tags to the database
	 * 
	 * @param array|string tags to enter
	 * @return array keys of the tags entered 
	 */
	public function add($tags) {
		if(!is_array($tags) && !is_string($tags)) {
			trigger_error("Tags is of unvalid type!", E_USER_ERROR);
		}
		
		$tags = $this->clean($tags);
		
		$keys = array();
		$existingTags = array();

			
		$query = "SELECT id, tag FROM tags WHERE tag = '" 
		. implode("' OR tag = '", $tags) ."'";
		
		trigger_error($query, E_USER_NOTICE);
		$result = SQL::query($query);
		
		
		while($tag = $result->fetch_object()) {
			$existingTags[] = $tag->tag;
			$keys[] = $tag->id; 
		}
		
		$tagsToAdd = array_diff($tags, $existingTags);
		unset($existingTags);
		
		trigger_error("Object $object is getting linked with tag(s) '"
			. implode("', '", $tags). "' whereof the tag(s): '" 
			. implode("', '", $tagsToAdd)
			."' are new to the database.", E_USER_NOTICE) ;
			
			
		foreach($tagsToAdd as $tag) {
			if($tag != "" && is_string($tag)) 
			$query = "INSERT INTO tags (id, tag)"
				. "VALUES ('', '".SQL::escape($tag)."')";
			trigger_error("Executing query: '$query'", E_USER_NOTICE);				
				
			SQL::query($query);
			$keys[] = SQL::insertId();
		}
		
		if(empty($keys))
			return false;
		else 
			return $keys;			
	}	
	
	/**
	 * Disconnect tags from an object (stub)
	 * 
	 * @param int $object id to object we want to disconnect from
	 * @param array|string $tags tags to disconnect
	 */
	public function disconnect($object, $tags) {}
	
	/**
	 * Removes tags from database and all its object bindings (stub)
	 * 
	 * @param array|string $tags tags to remove
	 */	
	public function remove($tags) {} 
	
	/**
	 * Takes a tag string or tag array and cleans it to our tag format
	 * 
	 * @param string|array $tag
	 * @return array A clean tag array, ready for database insertion! 
	 */
	public function clean($tags) {
		if(!(is_string($tags) || is_array($tags)))
			trigger_error("Invalid type! Cannot be cleansed");
			
		if(is_string($tags))
			$tags = explode(",", $tags);

		echo "<pre>";
		print_r($tags);
		echo "</pre>";	
			
		foreach($tags as &$tag) {
			$tag = strtolower($tag);				
			$tag = htmlentities($tag, ENT_QUOTES, "UTF-8");
			$tag = trim($tag);
			$tag = str_replace(array(" ", "�", "�", "�")
				, array("_", "a", "a", "o"), $tag);			
		}
		
		return $tags;
	}
	
	/**
	 * Returns the tags of any given image
	 * 
	 * @param int $imageId the id of the image we want the tags for
	 */
	public function get($imageId) {
		if(!is_numeric($imageId))
			trigger_error("\$imageId is not an integer!", E_USER_ERROR);
		$result = SQL::query("SELECT tags.*, tags.id FROM taglinks"
			. " LEFT JOIN tags ON tags.id = taglinks.id"
			. " WHERE taglinks.obj_id = $imageId");
		$tags = array();
		while($t = $result->fetch_assoc())
			$tags[] = $t;
			
		return $tags;
	}
	
	
	
}

?>