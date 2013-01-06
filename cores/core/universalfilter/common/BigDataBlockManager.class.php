<?php

include_once("universalfilter/common/HashString.php");

/**
 * Keeps blocks of data in memory if possible, but otherwise, writes them to file
 * 
 * Difference with cache: It ALWAYS returns the item if the item is set less than ? time ago.
 *  => So no loss of data if cache disabled...
 * 
 * @todo TODO: clean directory when files are older than a certain time (cron?) (directory: sys_get_temp_dir()."/The-DataTank-BigDataBlockManager" )
 *
 * @package The-Datatank/universalfilter/common
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
class BigDataBlockManager {
    //private static $BLOCKTIMEOUT = 216000;//60*60*60 sec
    private static $COUNT_KEEP_IN_MEMORY = 200;
    
    private static $instance;
    
    
    //the things in memory:
    private $memoryblockarray;
    private $issavedtofile;//only for those currently in memory
    
    private $currentcountinmemory = 0;
    
    
    
    public function __construct() {
        $this->memoryblockarray = new stdClass();
        $this->issavedtofile = new stdClass();
    }
    
    
    
    private function getDirToWriteTo(){
        // is this directory correct?????
        $tmpdir = getcwd() . "\\" .  "tmp\\";       
        return $tmpdir."The-DataTank-BigDataBlockManager_block_";
    }
    
    private function fileNameFor($key){
        return ($this->getDirToWriteTo()).hashWithNoSpecialChars($key).".datablock";
    }
    
    /*
     * The strategy for deleting blocks from memory, writing them to file, ...
     * 
     * Current implementation: LRU (least recently used)
     */
    
    //things used to implement LRU
    private $usetimelist = array();
    private $currentaccesstime = 0;
    
    /**
     * Makes space in memory...
     * 
     * Picks a block from memory and deletes it (writes it to file first)
     */
    private function kickABlockFromMemory(){
        $oldestaccesstime = $this->currentaccesstime+1;
        $oldestaccesskey = null;
        foreach ($this->usetimelist as $key => $lastaccesstime) {
            if($lastaccesstime<$oldestaccesstime){
                $oldestaccesstime = $lastaccesstime;
                $oldestaccesskey = $key;
            }
        }
        unset($this->usetimelist[$oldestaccesskey]);
        if(!$this->issavedtofile->$oldestaccesskey){
            $this->explicitSaveBlock($oldestaccesskey, $this->memoryblockarray->$oldestaccesskey);
        }
        unset($this->memoryblockarray->$oldestaccesskey);//remove from memory
        $this->currentcountinmemory--;
        //echo "DUMP (".$this->currentcountinmemory.")<br/>";
    }
    
    /**
     * Just save it... don't worry about the rest
     * @param string $key
     * @param object $value 
     */
    private function explicitSaveBlock($key, $value){
        //echo "save ".$key." (".$this->currentcountinmemory.")<br/>";
        $filename = $this->fileNameFor($key);
        $serializedValue = serialize($value);
        //WRITE FILE
        file_put_contents($filename, $serializedValue);
    }
    
    /**
     * Just put this block in memory... 
     *    don't worry about the number of items in memory 
     *    (that is handled elsewhere)
     * @param type $key
     * @param type $value 
     */
    private function explicitPutBlockInMemory($key, $value){
        $this->deleteBlock($key);//remove old data with the same key...
        $this->currentaccesstime++;//a counter to implement LRU
        $this->memoryblockarray->$key = $value;
        $this->usetimelist[$key] = $this->currentaccesstime;
        $this->issavedtofile->$key = false;
        $this->currentcountinmemory++;
        //echo "put ".$key." (".$this->currentcountinmemory.")<br/>";
    }
    
    /**
     * Delete the block with this key. ALSO from filesystem.
     * @param string $key 
     */
    private function deleteBlock($key){
        if(isset($this->memoryblockarray->$key)){
            //echo "del_mem ".$key." (".$this->currentcountinmemory.")<br/>";
            //kept and memory (and maybe also on file)
            unset($this->memoryblockarray->$key);
            unset($this->usetimelist[$key]);
            if($this->issavedtofile->$key){
                $filename = $this->fileNameFor($key);
                //DELETE FILE
                unlink($filename);
            }
            unset($this->issavedtofile->$key);
            //echo "[".$this->currentcountinmemory." -> ".($this->currentcountinmemory-1)."]";
            $this->currentcountinmemory--;
        }else{
            //echo "del_? ".$key." (".$this->currentcountinmemory.")<br/>";
            //kept on file only OR does not exist
            if($this->blockExistsOnFile($key)){
                $filename = $this->fileNameFor($key);
                //DELETE FILE
                unlink($filename);
            }
        }
    }
    
    /**
     * Checks if a block exist on file...
     * @param string $key 
     */
    private function blockExistsOnFile($key){
        return file_exists($this->fileNameFor($key));
    }
    
    /**
     * Just load the block into memory... 
     *    don't worry about the number of items in memory. 
     *    (that is handled elsewhere)
     * @param string $key 
     */
    private function explicitLoadBlockFromFileInMemory($key){
        $filename = $this->fileNameFor($key);
        
        //READ FILE
        $content = file_get_contents($this->fileNameFor($key));
        
        $this->currentaccesstime++;//a counter to implement LRU
        $this->memoryblockarray->$key = unserialize($content);
        $this->usetimelist[$key] = $this->currentaccesstime;
        $this->currentcountinmemory++;
        //echo "load ".$key." (".$this->currentcountinmemory.")<br/>";
    }

    /**
     * Gets a block... 
     * @param string $key
     * @return object 
     */
    private function getBlock($key){
        if(!isset($this->memoryblockarray->$key)){
            if($this->blockExistsOnFile($key)){
                //not in memory... it got pushed out...
                if($this->currentcountinmemory>=BigDataBlockManager::$COUNT_KEEP_IN_MEMORY){
                    $this->kickABlockFromMemory();//kick another one out
                }
                //load it back in...
                $this->explicitLoadBlockFromFileInMemory($key);
            }else{
                //sorry, that block does not exist
                return null;
            }
        }
        
        //so now it's in memory...
        $this->currentaccesstime++;
        $this->usetimelist[$key]=$this->currentaccesstime;//last used...
        return $this->memoryblockarray->$key;
    }
    
    /**
     * Sets a block...
     * @param string $key
     * @param object $value 
     */
    private function setBlock($key, $value){
        if($this->currentcountinmemory>=BigDataBlockManager::$COUNT_KEEP_IN_MEMORY){
            $this->kickABlockFromMemory();
        }
        $this->explicitPutBlockInMemory($key, $value);
    }
    
    /*
     * The Public functions
     */
    
    /**
     * Sets a datablock
     * @param string $key
     * @param object $value 
     */
    public function set($key, $value){
        //echo "set: $key<br/>";
        $this->setBlock($key, $value);
    }
    
    /**
     * Gets a datablock
     * @param string $key
     * @return object 
     */
    public function get($key){
        //echo "get: $key<br/>";
        return $this->getBlock($key);
    }
    
    /**
     * Deletes a datablock
     * @param type $key 
     */
    public function delete($key){
        //echo "del: $key<br/>";
        $this->deleteBlock($key);
    }
    
    /**
     * returns an instance of this class
     * @return BigDataBlockManager
     */
    public static function getInstance(){
        if(!isset(BigDataBlockManager::$instance)){
            BigDataBlockManager::$instance = new BigDataBlockManager();
        }
        return BigDataBlockManager::$instance;
    }
}

?>
