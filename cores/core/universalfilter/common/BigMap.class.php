<?php

/**
 * Represents a map that can possibly grow very big...
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class BigMap {
    private $id;
    
//    private $map;
    
    public function __construct() {
        $id = uniqid();
    }
    
    public function setMapValue($name, $value){
//        $this->map[$name]=$value;
        $inst = BigDataBlockManager::getInstance();
        $inst->set("BIGMAP_".$this->id."_INFO_".$name, $value);
    }
    
    public function getMapValue($name, $default=null){
//        if(isset ($this->map[$name])){
//            return $this->map[$name];
//        }else{
//            return $default;
//        }
        $inst = BigDataBlockManager::getInstance();
        $info = $inst->get("BIGMAP_".$this->id."_INFO_".$name);
        if(is_null($info)){
            return $default;
        }else{
            return $info;
        }
    }
    
    public function containsMapValue($name){
//        return isset($this->map[$name]);
        return $this->getMapValue($name)!=null;
    }
}

?>
