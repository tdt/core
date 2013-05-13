<?php

/**
 * This class will handle all resources installed in de package directory
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan a t iRail.be>
 * @author Michiel Vancoillie
 */

namespace tdt\core\model;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\create\InstalledResourceCreator;
use tdt\core\model\resources\delete\InstalledResourceDeleter;
use tdt\exceptions\TDTException;

class InstalledResourceFactory extends AResourceFactory {

    private $directory;

    public function __construct() {
        $this->directory = __DIR__ . "/packages/installed/";
    }

    public function createCreator($package, $resource, $parameters, $RESTparameters) {
        $creator = new InstalledResourceCreator($package, $resource, $RESTparameters);
        foreach ($parameters as $key => $value) {
            $creator->setParameter($key, $value);
        }
        return $creator;
    }

    public function createReader($package, $resource, $parameters, $RESTparameters) {

        // location contains the full name of the file, including the .class.php extension
        $location = $this->getLocationOfResource($package, $resource);

        if (file_exists($this->directory . $location)) {
            include_once($this->directory  . $location);
            $classname = $this->getClassnameOfResource($package, $resource);
            $reader = new $classname($package, $resource, $RESTparameters);
            $reader->processParameters($parameters);
            return $reader;
        } else {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($this->directory . $location), $exception_config);
        }
    }

    public function hasResource($package, $resource) {
        $resource = DBQueries::hasInstalledResource($package, $resource);
        return isset($resource["present"]) && $resource["present"] >= 1;
    }

    public function createDeleter($package, $resource, $RESTparameters) {
        $deleter = new InstalledResourceDeleter($package, $resource, $RESTparameters);
        return $deleter;
    }

    public function makeDoc($doc) {
        //ask every resource we have for documentation

        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }

            foreach ($resourcenames as $resourcename) {

                $example_uri = DBQueries::getExampleUri($package, $resourcename);
                $location = $this->getLocationOfResource($package, $resourcename);

                // file can always have been removed after adding it as a published resource
                if (file_exists($this->directory . $location)) {
                    $classname = $this->getClassnameOfResource($package, $resourcename);
                    $doc->$package->$resourcename = new \stdClass();
                    include_once($this->directory . $location);
                    $doc->$package->$resourcename->documentation = $classname::getDoc();
                    $doc->$package->$resourcename->requiredparameters = $classname::getRequiredParameters();
                    $doc->$package->$resourcename->parameters = $classname::getParameters();
                    $doc->$package->$resourcename->example_uri = $example_uri;
                }
            }
        }
        return $doc;
    }

    public function makeDescriptionDoc($doc) {
        //ask every resource we have for documentation

        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }

            foreach ($resourcenames as $resourcename) {

                $example_uri = DBQueries::getExampleUri($package, $resourcename);
                $location = $this->getLocationOfResource($package, $resourcename);

                // file can always have been removed after adding it as a published resource
                if (file_exists($this->directory . $location)) {

                    $classname = $this->getClassnameOfResource($package, $resourcename);
                    $doc->$package->$resourcename = new \stdClass();
                    include_once($this->directory . $location);
                    $doc->$package->$resourcename->documentation = $classname::getDoc();
                    $doc->$package->$resourcename->example_uri = $example_uri;
                    $doc->$package->$resourcename->resource_type = "installed";
                    $doc->$package->$resourcename->location = $location;
                    $doc->$package->$resourcename->classname = $classname;
                }
            }
        }
        return $doc;
    }

    private function getCreationTime($package, $resource) {
        //if the object read is a directory and the configuration methods file exists,
        //then add it to the installed packages
        $location = $this->getLocationofResource($package, $resource);
        if (file_exists($this->directory . $location)) {
            return filemtime($this->directory . $location);
        }
        return 0;
    }

    private function getModificationTime($package, $resource) {
        // for an existing folder you can only get the last modification date in php, so
        return $this->getCreationTime($package, $resource);
    }

    protected function getAllResourceNames() {
        /**
         * Get all the physical locations of published installed resources
         */
        $resources = array();
        $installedResources = DBQueries::getAllInstalledResources();
        foreach ($installedResources as $installedResource) {
            if (!array_key_exists($installedResource["package"], $resources)) {
                $resources[$installedResource["package"]] = array();
            }
            $resources[$installedResource["package"]][] = $installedResource["resource"];
        }
        return $resources;
    }

    private function getLocationOfResource($package, $resource) {
        return DBQueries::getLocationofResource($package, $resource);
    }

    private function getClassnameOfResource($package, $resource) {
        return DBQueries::getClassnameOfResource($package, $resource);
    }

    /**
     * Put together the deletion documentation for installed resources
     */
    public function makeDeleteDoc($doc) {
        $d = new \stdClass();
        $d->doc = "Installed resources can be deleted by sending a DELETE HTTP request to the resource definition located in TDTAdmin/Resources. The physical file however will remain existant, but no longer published.";
        if (!isset($doc->delete)) {
            $doc->delete = new \stdClass();
        }
        $doc->delete->installed = new \stdClass();
        $doc->delete->installed = $d;
    }

    /**
     * Put together the creation documentation for installed resources
     */
    public function makeCreateDoc($doc) {

        $d = new \stdClass();
        $installedResource = new InstalledResourceCreator("", "", array());
        $d->doc = "You can PUT an installed resource when you have created a resource-class in the installed folder.";
        $d->parameters = $installedResource->documentParameters();
        $d->requiredparameters = $installedResource->documentRequiredParameters();

        if (!isset($doc->create)) {
            $doc->create = new \stdClass();
        }
        $doc->create->installed = new \stdClass();
        $doc->create->installed = $d;
    }

}

?>
