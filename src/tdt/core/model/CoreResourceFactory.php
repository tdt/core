<?php

/**
 * This class will handle all resources needed by the core. For instance the resources provided by the TDTInfo package.
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Michiel Vancoillie
 */

namespace tdt\core\model;

class CoreResourceFactory extends AResourceFactory {

    private $directory;
    private $namespace;

    public function __construct() {
        $this->directory = __DIR__ . "/packages/core/";
        $this->namespace = "tdt\\core\\model\\packages\\core\\";
    }

    protected function getAllResourceNames() {
        return array("tdtinfo" => array("resources", "packages","admin", "formatters", "visualizations","statistics"),
            "tdtadmin" => array("resources", "export")
        );
    }

    public function createCreator($package, $resource, $parameters, $RESTparameters) {
        //do nothing
    }

    public function createReader($package, $resource, $parameters, $RESTparameters) {

        $classname = $this->namespace . $package . "\\" . $package . $resource;
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
            $package = strtolower($package);
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }
            foreach ($resourcenames as $resourcename) {
                $resourcename = strtolower($resourcename);
                $classname = $this->namespace . $package . "\\" . $package . $resourcename;
                $doc->$package->$resourcename = new \stdClass();
                $doc->$package->$resourcename->documentation = $classname::getDoc();
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
        if (is_dir($this->directory . $package) && file_exists($this->directory . $package . "/" . $resource . ".class.php")) {
            return filemtime($this->directory . $package . "/" . $resource . ".class.php");
        }
        return 0;
    }

    private function getModificationTime($package, $resource) {
        // for an existing folder you can only get the last modification date in php, so
        return $this->getCreationTime($package, $resource);
    }

    public function makeDeleteDoc($doc) {
        //We cannot delete Core Resources
        $d = new \stdClass();
        $d->documentation = "You cannot delete core resources.";
        if (!isset($doc->delete)) {
            $doc->delete = new \stdClass();
        }
        $doc->delete->core = new \stdClass();
        $doc->delete->core = $d;
    }

    public function makeCreateDoc($doc) {
        //we cannot create Core Resources
    }

    public function makeUpdateDoc($doc) {
        // we cannot update Core Resources
    }

    public function getAllPackagesDoc() {
        //ask every resource we have for documentation
        $packages = array();
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            array_push($packages, $package);
        }
        return $packages;
    }

}

?>
