<?php

/**
 * This file contains the Jsonp printer.
 * @package The-Datatank/formatters
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@iRail.be>
 * @author Pieter Colpaert   <pieter@iRail.be>
 */

namespace tdt\core\formatters;

use tdt\exceptions\TDTException;
use tdt\core\utility\Config;
/**
 * This class inherits from the Json printer. It just needs the json value and it will add
 * some data to make the json into a jsonp message.
 */
class JsonpFormatter extends JsonFormatter {

    private $callback;

    public function __construct($rootname, $objectToPrint, $callback = "") {
        if ($callback != "") {
            $this->callback = $callback;
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("With Jsonp you should add a callback: &callback=yourfunctionname"), $exception_config);
        }
        parent::__construct($rootname, $objectToPrint);
    }

    public function printHeader() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json;charset=UTF-8");
    }

    public function printBody() {
        echo $this->callback . '(';
        parent::printBody();
        echo ')';
    }

    public static function getDocumentation() {
        return "Prints json but will wrap the output in the callback function specified";
    }

}

;
?>
