<?php
/**
 * This is a class which will return all the available resources definitions in this DataTank
 * 
 * @package The-Datatank/packages/TDTAdmin
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

class TDTAdminResources extends AReader{

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
	$o = $resmod->getAllDescriptionDoc();
	return $o;
    }

    public static function getDoc(){
	return "This resource contains all the resource definitions.";
    }
}

?>
