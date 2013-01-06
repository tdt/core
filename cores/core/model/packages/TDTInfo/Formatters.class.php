<?php
/**
 * Returns all formatters in TDT
 * 
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

class TDTInfoFormatters extends AReader{

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
        return $d->visitAllFormatters();
    }

    public static function getDoc(){
	return "Returns all formatters in this DataTank.";
    }
    

}

?>
