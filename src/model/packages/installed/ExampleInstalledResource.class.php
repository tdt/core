<?php

/*
 * This is an example of how a installed resource is implemented
 *
 * @package The-Datatank/model/packages/installed/
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3 
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\installed;

class ExampleInstalledResource extends AInstalledResource{
    
    protected function setParameter($key, $value) {
        
    }

    public function read() {
        $example = new \stdClass();
        $example->firstword = "hello";
        $example->secondword = "world!";
        return $example;
    }
    
    public static function getParameters(){
	return array();
    }

    public static function getRequiredParameters(){
	return array();
    }  

    public static function getDoc(){
	return "This is an example class of how an installed resource can work.";
    }
    
}

?>
