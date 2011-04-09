<?php


/**
 * Creates a tag for usage in RSS Items
 * @package i
 */
class RSSBuilderTag {
    public $name;
    public $content;
    private $attr;

    /**
     * Sets default values of our RSS Tag
     *
     * @param $name Tag name
     * @param $content Tag content
     * @param $attr key/value representation of attributes in the tag
     */    
    public function __construct($name, $content = "", $attr = null) {
        $this->name = $name;
        $this->content = $content;
        
        if(is_array($attr))
            $this->attr = $attr;
        else
            $this->attr = null;
    }
    
    /**
     * Creates a string out of entered attributes
     * @return string representation of current attributes
     */
    private function makeAttrString() {
        if(!isset($this->attr))
            return "";

        $str = "";
        foreach($this->attr as $a => $val)
            $str .= " $a='$val'";
            
        return $str;
    }
    
    /**
     * Makes the RSSBuilderTag into a string for insertion in a RSSBuilderItem
     */    
    public function asString() {
        return "<$this->name".$this->makeAttrString().">$this->content</$this->name>";
    }
}


?>
