<?php
/**
 * Returns all visualizations in TDT
 * 
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

class TDTInfoVisualizations extends AReader{

    public static function getParameters(){
	return array();
    }

    public static function getRequiredParameters(){
	return array();
    }

    public function setParameter($key,$val){
        //we don't have any parameters
    }

    public function read(){
        $d = new Doc();
        return $d->visitAllVisualizations();
    }

    public static function getDoc(){
	return "Returns all visualizations in this DataTank.";
    }
    

}

?>
