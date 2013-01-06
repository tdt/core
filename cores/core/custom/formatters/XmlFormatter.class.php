<?php
/**
 * This file contains the Xml formatter.
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

/**
 * This class inherits from the abstract Formatter. It will our resultobject into an XML datastructure.
 */
class XmlFormatter extends AFormatter{
    //make a stack of array information, always work on the last one
    //for nested array support
    private $stack = array();
    private $arrayindices = array();
    private $currentarrayindex = -1;

    public function __construct($rootname,$objectToPrint){
        parent::__construct($rootname,$objectToPrint);
    }

    public function printHeader(){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: text/xml;charset=UTF-8");
    }

    public function printBody(){
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";	  
        $rootname = $this->rootname;
        
        if(!isset($this->objectToPrint->$rootname)){            
            $rootname = ucfirst($this->rootname);
            $this->rootname = $rootname;
        }

        
        if(!is_object($this->objectToPrint->$rootname)){
            $wrapper = new stdClass();            
            $wrapper->$rootname = $this->objectToPrint->$rootname;
            $this->objectToPrint->$rootname = $wrapper;
        }
        
        $this->printObject($this->rootname . " version=\"1.0\" timestamp=\"" . time() . "\"",$this->objectToPrint->$rootname);
        echo "</$this->rootname>";
    }

    private function printObject($name,$object,$nameobject=null){

        //check on first character
        if(preg_match("/^[0-9]+.*/", $name)){
            $name = "i" . $name; // add an i
        }
        $name = utf8_encode($name);
        echo "<".$name;
        //If this is not an object, it must have been an empty result
        //thus, we'll be returning an empty tag
        if(is_object($object)){
            $hash = get_object_vars($object);
            $tag_close = FALSE;
            
            foreach($hash as $key => $value){
                if(is_object($value)){
                    if($tag_close == FALSE){
                        echo ">";
                    }

                    $tag_close = TRUE;
                    $this->printObject($key,$value);                    
                }elseif(is_array($value)){
                    if($tag_close == FALSE){
                        echo ">";
                    }
                    $tag_close = TRUE;
                    $this->printArray($key,$value);                                     
                }else{
                    
                    if($key == $name){                       
                        echo ">" . htmlspecialchars($value, ENT_QUOTES);
                        $tag_close = TRUE;
                    }else{                        
                        $key = htmlspecialchars(str_replace(" ","",$key));
                        $key = utf8_encode($key);
                        
                        $value = htmlspecialchars($value, ENT_QUOTES);                         
                        $value = utf8_encode($value);
                        
                        if($this->isNotAnAttribute($key)){  
                            if(!$tag_close){
                                echo ">";
                                $tag_close = TRUE;
                            }
                            
                            echo "<$key>" . $value . "</$key>";
                        }else{
                            // To be discussed: strip the _ or not to strip the _
                            //$key = substr($key, 1);
                            echo " $key=" .'"' .$value.'"';
                        }                                                 
                    }
                }
            }

            if($tag_close == FALSE){
                echo ">";
            }
            
            
            if($name != $nameobject){
                $boom = explode(" ",$name);
                if(count($boom) == 1){
                    $name = utf8_encode($name);
                    echo "</$name>";
                }
            }

        }
    }

    private function isNotAnAttribute($key){
        //echo strpos($key,"_");
        return $key[0] != "_";
    }
    
    private function printArray($name,$array){
        //check on first character
        if(preg_match("/^[0-9]+.*/", $name)){
            $name = "i" . $name; // add an i
        }
        $index = 0;

        if(empty($array)){
            $name = utf8_encode($name);
            echo "<$name></$name>";
        }

        foreach($array as $key => $value){            
            $nametag = $name;
            if(is_object($value)){
                $this->printObject($nametag,$value,$name);
                $name = utf8_encode($name);
                echo "</$name>";
            }else if(is_array($value) && !$this->isHash($value)){
                $name = utf8_encode($name);
                echo "<".$name. ">";
                $this->printArray($nametag,$value);
                $name = utf8_encode($name);
                echo "</".$name.">";
            }else if(is_array($value) && $this->isHash($value)){
                $name = utf8_encode($name);
                echo "<".$name. ">";
                $this->printArray($key,$value);
                $name = utf8_encode($name);
                echo "</".$name.">";
            }else{// no array in arrays are allowed!!
                $name = htmlspecialchars(str_replace(" ","",$name));  
                $name = utf8_encode($name);
                
                $value = htmlspecialchars($value);
                $value = utf8_encode($value);
                
                $key = htmlspecialchars(str_replace(" ","",$key));
                $key = utf8_encode($key);
                
                if($this->isHash($array)){ 
                    //if this is an associative array, don't print it by name of the parent
                    //check on first character
                    if(preg_match("/^[0-9]+.*/", $key)){
                        $key = "i" . $key; // add an i
                    }
                    echo "<".$key . ">" . $value . "</".$key.">";
                }else{
                    echo "<".$name. ">".$value."</".$name.">";
                }
                    
            }  
            $index++;
        }
    }

    // check if we have an hash or a normal 'numberice array ( php doesn't know the difference btw, it just doesn't care. )
    private function isHash($arr){
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function getDocumentation(){
        return "Prints plain old XML. Watch out for tags starting with an integer: an i will be added.";
    }


};
?>