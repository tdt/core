<?php

/**
 * A universal representation of a table.
 * (This class is just a group for the header and content classes...)
 *
 * @package The-Datatank/universalfilter/data
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class UniversalFilterTable {
    private $header;
    private $content;

    /**
     * Makes a new table.
     * @param UniversalFilterTableHeader $header
     * @param UniversalFilterTableContent $content 
     */
    public function __construct($header, $content) {
        $this->header=$header;
        $this->content=$content;
    }
    
    /**
     * returns the header of this table
     * @return UniversalFilterTableHeader
     */
    public function getHeader(){
        return $this->header;
    }
    
    /**
     * returns the content of this table
     * @return UniversalFilterTableContent
     */
    public function getContent(){ 
        return $this->content;
    }
    
    /**
     * sets the header of this table
     * @param UniversalFilterTableHeader $header 
     */
    public function setHeader(UniversalFilterTableHeader $header){
        $this->header = $header;
    }
    
    /**
     * sets the content of this table
     * @param UniversalFilterTableContent $content 
     */
    public function setContent(UniversalFilterTableContent $content){
        $this->content = $content;
    }
}
?>
