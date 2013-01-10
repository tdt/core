<?php

/**
 * This class will handle all resources needed by the core. For instance the resources provided by the TDTInfo package.
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 */

namespace model;

class CoreResourceFactory extends AResourceFactory {

    
    public function __construct(){        
    }
    
    protected function getAllResourceNames() {
        return array("TDTInfo" => array("Resources", "Packages", "Exceptions", "Admin", "Formatters", "Visualizations"),           
            "TDTAdmin" => array("Resources", "Export")
        );
    }

    public function createCreator($package, $resource, $parameters, $RESTparameters) {
        //do nothing
    }

    public function createReader($package, $resource, $parameters, $RESTparameters) {
        include_once("/core/model/packages/" . $package . "/" . $resource . ".class.php");
        $classname = $package . $resource;
        $creator = new $classname($package, $resource, $RESTparameters);
        $creator->processParameters($parameters);
        return $creator;
    }

    public function createDeleter($package, $resource, $RESTparameters) {
        //do nothing
    }

    public function makeDoc($doc) {
        //ask every resource we have for documentation
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new StdClass();
            }
            foreach ($resourcenames as $resourcename) {
                $classname = $package . $resourcename;
                $doc->$package->$resourcename = new StdClass();
                include_once("core/model/packages/" . $package . "/" . $resourcename . ".class.php");
                $doc->$package->$resourcename->doc = $classname::getDoc();
                $doc->$package->$resourcename->requiredparameters = $classname::getRequiredParameters();
                $doc->$package->$resourcename->parameters = $classname::getParameters();
            }
        }
    }

    public function makeDescriptionDoc($doc) {
        $this->makeDoc($doc);
    }

    private function getCreationTime($package, $resource) {
        //if the object read is a directory and the configuration methods file exists, 
        //then add it to the installed packages
        if (is_dir("core/model/packages/" . $package) && file_exists("core/model/packages/" . $package . "/" . $resource . ".class.php")) {
            return filemtime("core/model/packages/" . $package . "/" . $resource . ".class.php");
        }
        return 0;
    }

    private function getModificationTime($package, $resource) {
        // for an existing folder you can only get the last modification date in php, so 
        return $this->getCreationTime($package, $resource);
    }

    public function makeDeleteDoc($doc) {
        //We cannot delete Core Resources
        $d = new StdClass();
        $d->doc = "You cannot delete core resources.";
        if (!isset($doc->delete)) {
            $doc->delete = new StdClass();
        }
        $doc->delete->core = new StdClass();
        $doc->delete->core = $d;
    }

    public function makeCreateDoc($doc) {
        //we cannot create Core Resources
    }

    public function makeUpdateDoc($doc) {
        // we cannot update Core Resources
    }

    public function getAllPackagesDoc(){
        //ask every resource we have for documentation
        $packages = array();
        foreach($this->getAllResourceNames() as $package => $resourcenames){
            array_push($packages,$package);
        }
        return $packages;
    }
}

?>
