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

namespace tdt\core\model\packages\core\TDTInfo;

use tdt\core\model\resources\read\AReader;
use tdt\core\model\ResourcesModel;
use tdt\core\utility\Config;
use tdt\exceptions\TDTException;

class TDTInfoAdmin extends AReader {

    public static function getParameters() {
        return array();
    }

    public static function getRequiredParameters() {
        return array();
    }

    public function setParameter($key, $val) {
        //we don't have any parameters
    }

    public function read() {
        $resmod = ResourcesModel::getInstance(Config::getConfigArray());
        $result_object = $resmod->getAllAdminDoc();

        foreach($this->RESTparameters as $param){
            if (is_object($result_object) && $this->isPropertySet($result_object,$param)) {
                $param = $this->isPropertySet($result_object,$param);
                $result_object = $result_object->$param;
            }else if (is_array($result_object)){
                // Lower case the keys and check if $param is an entry
                $result_object = array_change_key_case($result_object); // Default is lower case.
                if(isset($result_object[$param])){
                    $result_object = $result_object[$param];
                }else{
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(404, array("The REST parameters $param hasn't been found, check if the hierarchy is correct, or spelling errors have been made."), $exception_config);
                }
            }else {
                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(404, array("The REST parameters $param hasn't been found, check if the hierarchy is correct, or spelling errors have been made."), $exception_config);
            }
        }
        return $result_object;
    }

    public static function getDoc() {
       return "This resource contains the information an Admin should know. It documents all possible addition, deletion and creation methods";
    }

    /**
     * check if a property is set in the object, the property to compare with is in lower case.
     */
    private function isPropertySet($object,$lower_property){
        $properties = get_object_vars($object);

        foreach($properties as $property => $value){
            if(strtolower($property) == $lower_property){
                return $property;
            }
        }
        return false;
    }
}

