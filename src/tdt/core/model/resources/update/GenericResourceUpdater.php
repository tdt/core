<?php

/**
 * This will proxy the updater to a generic strategy resource
 * 
 * @package The-Datatank/model/resources/update
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\update;

use tdt\exceptions\TDTException;

class GenericResourceUpdater extends AUpdater {

    private $strategy;
    private $generic_type;

    public function __construct($package, $resource, $RESTparameters, $generic_type) {
        parent::__construct($package, $resource, $RESTparameters);
        $this->generic_type = $generic_type;
        if (!class_exists("tdt\\core\\strategies\\" . $this->generic_type)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("Generic type does not exist: " . $this->generic_type) . ".", $exception_config);
        }

        $classname = "tdt\\core\\strategies\\" . $this->generic_type;
        // add all the parameters to the $parameters
        // and all of the requiredParameters to the $requiredParameters
        $this->strategy = new $classname();
        $this->strategy->package = $package;
        $this->strategy->resource = $resource;
    }

    public function getParameters() {
        $parameters = array("documentation");
        return array(
        );
    }

    public function getRequiredParameters() {
        return array(
        );
    }

    protected function setParameter($key, $value) {
        $this->$key = $value;
    }

    public function update() {
        
    }

    public function getDocumentation() {
        return "Perform an update on a resource, all PUT (create) properties can be changed through this action.";
    }

}

?>