<?php
/**
 * An class for XML data
 *
 * @package The-Datatank/model/resources/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */


namespace tdt\core\strategies;

use tdt\exceptions\TDTException;
use tdt\core\model\resources\AResourceStrategy;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use tdt\core\utility\Config;
use tdt\core\utility\Request;

include_once(__DIR__ . "/../../../../includes/XMLUtils.class.php");

class XML extends AResourceStrategy{

    public function read(&$configObject,$package,$resource){
        $resultObj = \XmlUtils::xmlFileToObject($configObject->uri);
        return $resultObj;
    }

    public function onUpdate($package, $resource){

    }

    public function documentCreateRequiredParameters(){
        return array("uri");
    }

    public function documentReadRequiredParameters(){
        return array();
    }

    public function isValid($package_id, $gen_resource_id){
        if(empty($this->uri)){
            $this->throwException($package_id,$gen_resource_id,"The uri to the xml datasource wasn't passed, but is necessary to publish the xml data.");
        }

        $obj = \XmlUtils::xmlFileToObject($this->uri);

        if(!$obj){
            $this->throwException($package_id,$gen_resource_id,"The uri to the data couldn't be successfully parsed to an xml object.");
        }
        return true;
    }


    public function documentUpdateRequiredParameters(){
        return array();
    }


    public function documentCreateParameters(){
        return array(
            "uri" => "The uri to the xml document."
        );
    }

    public function documentReadParameters(){
       return array();
    }

    public function documentUpdateParameters(){
       return array();
    }

    public function getFields($package,$resource){
       return array();
    }
}