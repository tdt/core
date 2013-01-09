<?php
/**
 * This is a class which will return all the packages in The DataTank
 * 
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\TDTInfo;

class TDTInfoPackages extends tdt\core\model\resources\read\AReader{

    public static function getParameters(){
	return array();
    }

    public static function getRequiredParameters(){
	return array();
    }

    public function setParameter($key,$val){
    }
    
    public function read(){
	$resmod = tdt\core\model\ResourcesModel::getInstance();
        $doc = $resmod->getAllPackagesDoc();
	return $doc;
    }
    
    public static function getDoc(){
	return "This resource contains every package installed on this DataTank instance.";
    }
}
?>
