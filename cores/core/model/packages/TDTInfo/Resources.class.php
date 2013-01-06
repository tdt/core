<?php
/**
 * This is a class which will return all the available resources along with the documentation of that resource in this DataTank
 * 
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

class TDTInfoResources extends AReader{

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
	$resmod = ResourcesModel::getInstance();
	$o = $resmod->getAllDoc();
	return $o;
    }

    public static function getDoc(){
	return "This resource contains the documentation of all resources.";
    }
}

?>
