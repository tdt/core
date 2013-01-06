<?php

/**
 * Represents a list that can possibly grow very big...
 *
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class BigList {
    public static $BLOCKSIZE = 50;
    
    private $id;
    private $size;
    
    public function __construct() {
        $this->id = uniqid();
        $size = 0;
    }
    
    public function setIndex($index, $data) {
        if($index>=$this->size){
            throw new Exception("BigList: Index out of bounds: ".$index);
        }
        $inst = BigDataBlockManager::getInstance();
        $blockindex = floor($index/(BigList::$BLOCKSIZE));
        $indexInBlock = "v_".($index%(BigList::$BLOCKSIZE));
        
        $oldList = $inst->get("BIGLIST_".$this->id."_".$blockindex);//load the data
        if(is_null($oldList)){
            $oldList = new stdClass();
        }
        $oldList->$indexInBlock = $data;
        $inst->set("BIGLIST_".$this->id."_".$blockindex, $oldList);//save it again
    }
    
    public function getIndex($index) {
        if($index>=$this->size){
            throw new Exception("BigList: Index out of bounds ".$index);
        }
        $inst = BigDataBlockManager::getInstance();
        $blockindex = floor($index/(BigList::$BLOCKSIZE));
        $indexInBlock = "v_".($index%(BigList::$BLOCKSIZE));
        
        $oldList = $inst->get("BIGLIST_".$this->id."_".$blockindex);//load the data
        
        if(is_null($oldList)){
            $oldList = new stdClass();
        }
        return $oldList->$indexInBlock;
    }
    
    public function addItem($data){
        $this->size++;
        $this->setIndex($this->size-1, $data);
        if(floor(($this->size-1)/BigList::$BLOCKSIZE)!=floor(($this->size-2)/BigList::$BLOCKSIZE)){
            //echo "biglist expand... ".$this->id;
        }
    }
    
    public function getSize(){
        return $this->size;
    }
    
    public function destroy(){
        //echo "biglist destroyed... ".$this->id;
        $inst = BigDataBlockManager::getInstance();
        for($i=0;$i<=floor(($this->size-1)/BigList::$BLOCKSIZE);$i++){
            $inst->delete("BIGLIST_".$this->id."_".$i);
        }
        $this->size=0;
    }
}

?>
