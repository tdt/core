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

use tdt\core\model\resources\read\AReader;

class TDTInfoExceptions extends AReader {

    public static function getParameters() {
        return array();
    }

    public static function getRequiredParameters() {
        return array();
    }

    public function setParameter($key, $val) {

    }

    public function read() {

        $exceptions = parse_ini_file("custom/exceptions.ini", true);
        $tmp = array();

        foreach ($exceptions as $errorcode => $configarray) {

            $e = new stdClass();
            $e->code = $errorcode;
            $e->message = $configarray["message"];
            $e->parameters = $configarray["parameters"];
            $e->doc = $configarray["documentation"];
            $e->short = $configarray["short"];
            array_push($tmp, $e);
        }

        return $tmp;
    }

    public static function getDoc() {
        return "This resource contains every exception used by this DataTank instance.";
    }

}

?>
