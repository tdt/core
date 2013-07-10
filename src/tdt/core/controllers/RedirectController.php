<?php

/**
 * This controller will redirect the user for content negotiation
 * @package The-Datatank/controllers
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\controllers;

use tdt\core\utility\RequestURI;
use tdt\exceptions\TDTException;
use app\core\Config;

class RedirectController extends AController {

    public function __construct() {
        parent::__construct();
    }

    function HEAD($matches) {
        $this->GET($matches);
    }

    function POST($matches) {
        //get the current URL
        $ru = RequestURI::getInstance(Config::getConfigArray());
        $pageURL = $ru->getURI();
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("POST", $pageURL), $exception_config);
    }

    function PUT($matches) {
        //get the current URL
        $ru = RequestURI::getInstance(Config::getConfigArray());
        $pageURL = $ru->getURI();
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("PUT", $pageURL), $exception_config);
    }

    function DELETE($matches) {
        //get the current URL
        $ru = RequestURI::getInstance(Config::getConfigArray());
        $pageURL = $ru->getURI();
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("DELETE", $pageURL), $exception_config);
    }

    /**
     * You cannot use patch a representation
     */
    public function PATCH($matches) {
        //get the current URL
        $ru = RequestURI::getInstance(Config::getConfigArray());
        $pageURL = $ru->getURI();
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("PATCH", $pageURL), $exception_config);
    }

}