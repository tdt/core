<?php

/**
 * This is a class which will return all the packages in The DataTank
 *
 * @package The-Datatank/packages/TDTInfo
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\core\TDTInfo;

use tdt\core\model\resources\read\AReader;
use tdt\core\model\ResourcesModel;
use tdt\core\utility\Config;

class TDTInfoPackages extends AReader {

    public static function getParameters() {
        return array();
    }

    public static function getRequiredParameters() {
        return array();
    }

    public function setParameter($key, $val) {

    }

    public function read() {
        $resmod = ResourcesModel::getInstance(Config::getConfigArray());
        $result_object = $resmod->getAllPackagesDoc();

        foreach($this->RESTparameters as $param){
            if (is_object($result_object) && isset($result_object->$param)) {
                $result_object = $result_object->$param;
            }else if (is_array($result_object) && isset($result_object[$param])) {
                $result_object = $result[$param];
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
        return "This resource contains every package installed on this DataTank instance.";
    }

}

?>
