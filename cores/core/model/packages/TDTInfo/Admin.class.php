<?php
/**
 * This is a class which will return all the possible admin calls to this datatank
 * 
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert   <pieter@iRail.be>
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

class TDTInfoAdmin extends AReader{

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
	$o = $resmod->getAllAdminDoc();
	return $o;
    }
  
    public static function getDoc(){
	return "This resource contains the information an Admin should know. It documents all possible addition, deletion and creation methods";
    }
    

}

?>
