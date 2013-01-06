<?php
/**
 * This file contains the php formatter.
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

/**
 * This class inherits from the abstract Formatter. It will return our object in a php datastrucutre.
 */
class PhpFormatter extends AFormatter{
     
     public function __construct($rootname,$objectToPrint){
	  parent::__construct($rootname,$objectToPrint);
     }

     public function printHeader(){
	  header("Access-Control-Allow-Origin: *");
	  header("Content-Type: text/plain;charset=UTF-8"); 
     }

     public function printBody(){
	  if(is_object($this->objectToPrint)){
	       $hash = get_object_vars($this->objectToPrint);
	  }
	  $hash['version'] = $this->version;
	  $hash['timestamp'] = time();
	  echo serialize($hash);
     }

     public static function getDocumentation(){
         return "Prints php object notation. This can come in handy for php serialization";
     }

};
?>