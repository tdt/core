<?php

/**
 * This is a class which will return all the available resources definitions in this DataTank
 *
 * @package The-Datatank/packages/TDTAdmin
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\core\TDTAdmin;

use tdt\core\model\resources\read\AReader;
use tdt\core\model\ResourcesModel;
use tdt\core\utility\Config;
use tdt\exceptions\TDTException;

class TDTAdminResources extends AReader {

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
        $result_object = $resmod->getAllDescriptionDoc();

        /**
         * Take the REST parameters into account, we know that packages with subpackages are not separate objects but form 1 package name (e.g. demography/usa/numbers where numbers is the resourcename)
         * So let's ask the resourcesmodel what the package and if there is a resourcename attached with REST parameters.
         */
        if(count($this->RESTparameters) > 0){
            $package = implode("/",$this->RESTparameters);
            $result = $resmod->processPackageResourceString($package);

            $package = $result["packagename"];
            $resource = $result["resourcename"];

            /**
             * If no resourcename has been passed, check the package in the packagename, instead of the description documentation.
             */
            if(isset($result_object->$package)){
                $result_object = $result_object->$package;
            }else{
              $exception_config = array();
              $exception_config["log_dir"] = Config::get("general", "logging", "path");
              $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
              throw new TDTException(404, array("The REST filter given : '" . implode("/",$this->RESTparameters) . "' cannot be found in the tdtadmin/resources object."), $exception_config);
            }

            if($resource != null || $resource != ""){
                $result_object = $result_object->$resource;
            }


            /**
             * Take the rest of the RESTparameters into account
             */

            $RESTparameters = $result["RESTparameters"];
            while(!empty($RESTparameters)){
                $rp = array_shift($RESTparameters);
                if(is_object($result_object) && isset($result_object->$rp)){
                    $result_object = $result_object->$rp;
                }else if(is_array($result_object) && isset($result_object[$rp])){
                    $result_object = $result_object[$rp];
                }else{
                    $exception_config = array();
                    $exception_config["log_dir"] = Config::get("general", "logging", "path");
                    $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                    throw new TDTException(404, array("The REST parameters $rp hasn't been found, check if the hierarchy is correct, or spelling errors have been made."), $exception_config);
                }
            }


        }

        return $result_object;
    }

    public static function getDoc() {
        return "This resource contains all the resource definitions.";
    }

}

?>
