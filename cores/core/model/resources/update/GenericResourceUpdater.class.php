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

class GenericResourceUpdater extends AUpdater {

    private $strategy;
    private $generic_type;

    public function __construct($package, $resource, $RESTparameters,$generic_type) {
        parent::__construct($package, $resource, $RESTparameters);
        $this->generic_type = $generic_type;
        if(!file_exists(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/strategies/" . $this->generic_type . ".class.php")){
            throw new TDTException(452,array("Generic type does not exist: " . $this->generic_type).".");
        }
        include_once(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/strategies/" . $this->generic_type . ".class.php");
        // add all the parameters to the $parameters
        // and all of the requiredParameters to the $requiredParameters
        $this->strategy = new $this->generic_type();
        $this->strategy->package = $package;
        $this->strategy->resource = $resource;
    }

    public function getParameters(){
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