<?php

/**
 * This is a class which will return all the available resources definitions in this DataTank
 * 
 * @package The-Datatank/packages/TDTAdmin
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 */

namespace tdt\core\model\packages\TDTAdmin;

use tdt\core\model\resources\read\AReader;
use tdt\core\model\ResourcesModel;
use tdt\core\utility\Config;

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
        $o = $resmod->getAllDescriptionDoc();
        return $o;
    }

    public static function getDoc() {
        return "This resource contains all the resource definitions.";
    }

}

?>
