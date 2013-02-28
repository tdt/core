<?php

/**
 * This class will handle the export of resources
 *
 * @package The-Datatank/model/packages/TDTAdmin
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\packages\core\TDTAdmin;

use tdt\cache\Cache;
use tdt\core\utility\Config;
use tdt\core\model\resources\read\AReader;


class TDTAdminDocreset extends AReader{

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

        try{
            $cache_config = array();

            $cache_config["system"] = Config::get("general", "cache", "system");
            $cache_config["host"] = Config::get("general", "cache", "host");
            $cache_config["port"] = Config::get("general", "cache", "port");

            $c = Cache::getInstance($cache_config);

            $hostname = Config::get("general","hostname");
            $subdir = Config::get("general","subdir");

            $c->delete($hostname . $subdir . "documentation");
            $c->delete($hostname . $subdir . "descriptiondocumentation");
            $c->delete($hostname . $subdir . "admindocumentation");
            $c->delete($hostname . $subdir . "packagedocumentation");
            echo "Documentation has been reset!";
        }catch(Exception $ex){
            echo "Something went wrong whilst resetting the documentation: $ex->getMessage().";
        }
        exit();
    }

    public static function getDoc() {
        return "This resource contains all the resource definitions.";
    }

}