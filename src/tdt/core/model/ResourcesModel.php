<?php

/**
 * This is the model for our application. You can access everything from here
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model;

use tdt\core\model\CoreResourceFactory;
use tdt\core\model\DBQueries;
use tdt\core\model\Doc;
use tdt\core\model\GenericResourceFactory;
use tdt\core\model\InstalledResourceFactory;
use tdt\core\model\RemoteResourceFactory;
use tdt\core\model\resources\GenericResource;
use tdt\core\model\ResourcesModel;
use tdt\core\universalfilter\UniversalFilterNode;
use tdt\framework\Cache\Cache;
use tdt\core\utility\Config;
use tdt\framework\TDTException;
use RedBean_Facade as R;


class ResourcesModel {
    /*
     * installation variables
     */

    private $host;
    private $subdir;
    private static $instance;
    private $factories; //array of factories
    private $updateActions;

    private function __construct() {
        $this->host = Config::get("general", "hostname");
        $this->subdir = Config::get("general", "subdir");

        $this->factories = array(); //(ordening does matter here! Put the least expensive on top)
        $this->factories["generic"] = new GenericResourceFactory();
        $this->factories["core"] = new CoreResourceFactory();
        $this->factories["remote"] = new RemoteResourceFactory();
        $this->factories["installed"] = new InstalledResourceFactory();

        /*
         * This array maps all the update types to the correct delegation methods
         * these methods are methods that are part of the resourcemodel, but are not
         * part of the resource itself. i.e. a foreign relation between two resources
         */
        $this->updateActions = array();

        //Added for linking this resource to a class descibed in an onthology
        $this->updateActions["generic"] = "GenericResourceUpdater";
    }

    public static function getInstance() {
        R::setup(Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"), Config::get("db", "user"), Config::get("db", "password"));
        if (!isset(self::$instance)) {
            self::$instance = new ResourcesModel();
        }
        return self::$instance;
    }

    /**
     * Checks if a package exists
     */
    public function hasPackage($package) {
        $doc = $this->getAllPackagesDoc();
        foreach ($doc as $packagename => $resourcenames) {
            if ($package == $packagename) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks the doc whether a certain resource exists in our system.
     * We will look for a definition in the documentation. Of course,
     * the result of the documentation visitor class will be cached
     *
     * @return a boolean
     */
    public function hasResource($package, $resource) {
        $doc = $this->getAllDoc();              
        foreach ($doc as $packagename => $resourcenames) {
            if ($package == $packagename) {
                foreach ($resourcenames as $resourcename => $var) {
                    if ($resourcename == $resource) {                        
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Creates the given resource
     * @param string $package The package name under which the resource will exist.
     * @param string $resource The resource name under which the resource will be called.
     * @param array $parameters An array with create parameters
     * @param array $RESTparameters An array with additional RESTparameters
     */
    public function createResource($packageresourcestring, $parameters) {
        
        /**
         * Hierachical package/resource structure
         * check if the package/resource structure is correct
         */
        $pieces = explode("/", $packageresourcestring);
        //throws exception when it's not valid, returns packagestring when done
        $package = $this->isResourceValid($pieces);
        $resource = array_pop($pieces);

        // NOTE: not sure why we used RESTparameters with a create for a resource, can't really think of any
        // case where RESTparameters should be used for the creation of a resource. Will not adjust
        // the code for now, using a dummy array().
        $RESTparameters = array();


        //if it doesn't, test whether the resource_type has been set
        if (!isset($parameters["resource_type"])) {
            throw new TDTException(452, array("Parameter resource_type hasn't been set"));
        }

        /**
         * adding some semantics to the resource_type parameter
         * generic/generic_type should be parsed as generic being the resource_type and generic_type as the
         * generic type, without passing that as a separate parameter
         * NOTE that passing generic/generic_type has priority over generic_type = ...
         */
        $resourceTypeParts = explode("/", $parameters["resource_type"]);
        if ($resourceTypeParts[0] != "remote" && $resourceTypeParts[0] != "installed") {
            if ($resourceTypeParts[0] == "generic" && !isset($parameters["generic_type"])
                    && isset($resourceTypeParts[1])) {
                $parameters["generic_type"] = $resourceTypeParts[1];
                $parameters["resource_type"] = $resourceTypeParts[0];
            } else if (!isset($parameters["generic_type"])) {
                throw new TDTException(452, array("Parameter generic_type hasn't been set, or the combination generic/generic_type hasn't been properly passed. A template-example: generic/CSV"));
            }
        }


        $restype = $parameters["resource_type"];
        $restype = strtolower($restype);
        //now check if the file exist and include it
        if (!in_array($restype, array("generic", "remote", "installed"))) {
            throw new TDTException(452, array("Resource type doesn't exist. Choose from generic,remote or installed"));
        }
        // get the documentation containing information about the required parameters
        $doc = $this->getAllAdminDoc();

        /**
         * get the correct requiredparameters list to check
         */
        $resourceCreationDoc;
        if ($restype == "generic") {
            /*
             * Issue: keys of an array cannot be gotten without an exact match, csv != CSV is an example
             * of a result of this matter, this however should be ==
             * Solution : fetch all the keys, compare them strtoupper ( or lower, matter of taste ) , then replace
             * generic_type with the "correct" one
             */
            
            $parameters["generic_type"] = $this->formatGenericType($parameters["generic_type"], $doc->create->generic);
            $resourceCreationDoc = $doc->create->generic[$parameters["generic_type"]];
        } elseif ($restype == "remote") {
            $resourceCreationDoc = $doc->create->remote;
        } elseif ($restype == "installed") {
            $resourceCreationDoc = $doc->create->installed;
        }


        /**
         * Check if all required parameters are being passed
         */
        foreach ($resourceCreationDoc->requiredparameters as $key) {
            if (!isset($parameters[$key])) {
                throw new TDTException(452, array("Required parameter " . $key . " has not been passed"));
            }
        }

        //now check if there are nonexistent parameters given
        foreach (array_keys($parameters) as $key) {
            if (!in_array($key, array_keys($resourceCreationDoc->parameters))) {
                throw new TDTException(452, array("The parameter $key is non existent for the given type of resource."));
            }
        }


        // all is well, let's create that resource!        
        $creator = $this->factories[$restype]->createCreator($package, $resource, $parameters, $RESTparameters);
        try {
            //first check if there resource exists yet
            if ($this->hasResource($package, $resource)) {                  
                //If it exists, delete it first and continue adding it.
                //It could be that because errors occured after the addition, that
                //the documentation reset in the CUDController isn't up to date anymore
                //This will result in a hasResource() returning true and deleteResource returning false (error)
                //This is our queue to reset the documentation.
                try {
                    $this->deleteResource($package, $resource, $RESTparameters);
                } catch (Exception $ex) {
                    //Clear the documentation in our cache for it has changed
                    $this->clearCachedDocumentation();
                    throw new TDTException(500, array("Error: " . $ex->getMessage() . " We've done a hard reset on the internal documentation, try adding it again. If this doesn't work please log on issue or e-mail one of the developers."));
                }
            }
        } catch (Exception $ex) {
            //Clear the documentation in our cache for it has changed
            $this->deleteResource($package, $resource, $RESTparameters);
            throw new TDTException($ex->getMessage());
        }
        $creator->create();
    }

    private function clearCachedDocumentation() {
        $c = Cache::getInstance();
        $c->delete($this->host . $this->subdir . "documentation");
        $c->delete($this->host . $this->subdir . "admindocumentation");
        $c->delete($this->host . $this->subdir . "packagedocumentation");
    }

    /**
     * This function doesn't return anything, but throws exceptions when the validation fails
     */
    private function isResourceValid($pieces) {
        /**
         * build the package/resource from scratch and check with every step
         * if the condition isn't false, the condition to add the resource is:
         *  ((1)) a package/resource cannot replace a package, example:
         * we have a package called X/Y/Z and our new package/resource is also called X/Y/Z
         * this cannot be tolerated as we would then delete an entire package (and all of its resources) to add a new resource
         * you can thus only replace/renew resource with resources.
         *  ((2)) the package so far built (first X, then X/Y, then X/Y/Z in our example) cannot be a resource
         * so we have to built the package first, and check if it's not a resource
         */
        /**
         * If we have only 1 package entry (resource consists of 1 hierarchy of packages i.e. package/resource)
         * then we can return true, because a package/resource may overwrite an existing package/resource
         */
        $resource = array_pop($pieces);
        if (count($pieces) == 1) {
            return $pieces[0];
        }
        /**
         * check if the packagestring isn't a resource ((2))
         */
        $packagestring = array_shift($pieces);

        foreach ($pieces as $package) {
            if ($this->isResource($packagestring, $package)) {
                throw new TDTException(452, array($packagestring . "/" . $package . " is already a resource, you cannot overwrite resources with packages!"));
            }
            $packagestring .= "/" . $package;
        }

        /**
         * check if the resource isn't a package ((1))
         */
        $resourcestring = $packagestring . "/" . $resource;
        if ($this->isPackage($resourcestring)) {
            throw new TDTException(452, array($resourcestring . " is already a packagename, you cannot overwrite a package with a resource."));
        }
        return $packagestring;
    }

    /*
     * Analyses a URI, and returns package, resource and RESTparameters,
     * in contrast with "isResourceValid" this will not return exceptions
     * because it doesn't assume that the package/resource is the only thing
     * in the URI. This function copes with RESTparameters as well.
     *
     * It will look for the first valid string that matches a resource and return it,
     * as well with the RESTparameters and packagestring. If no resource is identified, it returns an exception
     */

    public function fetchPackageAndResource($pieces) {
        $result = array(); // contains package, resource, RESTparameters
        $RESTparameters = array();

        $package = array_shift($pieces);

        foreach ($pieces as $piece) {
            if ($this->isResource($package, $piece)) {
                $result["package"] = $package;
                $result["resource"] = $piece;
                array_shift($pieces);
                $result["RESTparameters"] = $pieces;
                return $result;
            } else {
                $package.= $package . "/" . $piece;
                array_shift($pieces);
            }
        }
    }

    private function isPackage($needle) {
        $result = DBQueries::getPackageId($needle);
        return $result != NULL;
    }

    private function isResource($package, $subpackage) {
        $result = DBQueries::getResourceType($package, $subpackage);
        return $result != NULL;
    }

    /**
     * Searches for a generic entry in the generic- create part of the documentation, independent of
     * how it is passed (i.e. csv == CSV )
     * @return The correct entry in the generic table ( csv would be changed with CSV )
     */
    private function formatGenericType($genType, $genericTable) {
        foreach ($genericTable as $type => $value) {
            if (strtoupper($genType) == strtoupper($type)) {
                return $type;
            }
        }
        throw new TDTException(452, array($genType . " was not found as a generic_type."));
    }

    /**
     * Reads the resource with the given parameters
     * @param string $package The package name under which the resource exists.
     * @param string $resource The resource name.
     * @param array $parameters An array with read parameters
     * @param array $RESTparameters An array with additional RESTparameters
     */
    public function readResource($package, $resource, $parameters, $RESTparameters) {

        //first check if the resource exists
        if (!$this->hasResource($package, $resource)) {
            throw new TDTException(452, array("package/resource pair: $package, $resource was not found."));
        }

        foreach ($this->factories as $factory) {
            if ($factory->hasResource($package, $resource)) {
                $reader = $factory->createReader($package, $resource, $parameters, $RESTparameters);
                return $reader->execute();
            }
        }
    }

    /**
     * Updates the resource definition with the given parameters.
     * @param string $package The package name
     * @param string $resource The resource name
     * @param array $parameters An array with update parameters
     * @param array $RESTparameters An array with additional RESTparameters
     */
    public function updateResource($package, $resource, $parameters, $RESTparameters) {

        //first check if the resource exists
        if (!$this->hasResource($package, $resource)) {
            throw new TDTException(452, array("package/resource pair: $package, $resource was not found."));
        }

        /**
         * Get the resource properties from the documentation
         * Replace that passed properties and re-add the resource
         */
        $doc = $this->getAllDescriptionDoc();
        $currentParameters = $doc->$package->$resource;

        /**
         * Strip non create parameters from the definition
         */
        unset($currentParameters->parameters);
        unset($currentParameters->requiredparameters);
        
        if(isset($currentParameters->remote_package)){
            unset($currentParameters->documentation);
        }
        
        unset($currentParameters->remote_package);        
        unset($currentParameters->resource);

        foreach ($parameters as $parameter => $value) {
            if ($value != "" && $parameter != "columns") {
                $currentParameters->$parameter = $value;
            }
        }

        /**
         * Columns aren't key => value datamembers and will be handled separatly
         */
        if (isset($currentParameters->columns) && isset($parameters["columns"])) {
            foreach ($parameters["columns"] as $index => $value) {
                $currentParameters->columns[$index] = $value;
            }
        }
        
        // delete the empty parameters from the currentParameters object
        foreach ((array) $currentParameters as $key => $value) {
            if ($value == "") {
                unset($currentParameters->$key);
            }
        }
        $currentParameters = (array) $currentParameters;
        $this->createResource($package . '/' . $resource, $currentParameters);
    }

    /**
     * Deletes a Resource
     * @param string $package The package name
     * @param string $resource The resource name
     * @param array $parameters An array with delete parameters
     * @param array $RESTparameters An array with additional RESTparameters
     */
    public function deleteResource($package, $resource, $RESTparameters) {

        //first check if the resource exists
        if (!$this->hasResource($package, $resource)) {
            throw new TDTException(452, array("package/resource pair: $package, $resource was not found."));
        }

        /**
         * We only support the deletion of generic and remote resources and packages by
         * an API call.
         */
        $factory = "";
        if ($this->factories["generic"]->hasResource($package, $resource)) {
            $factory = $this->factories["generic"];
        } else if ($this->factories["remote"]->hasResource($package, $resource)) {
            $factory = $this->factories["remote"];
        } else if ($this->factories["installed"]->hasResource($package, $resource)) {
            $factory = $this->factories["installed"];
        } else {
            throw new TDTException(452, array("package/resource pair: $package, $resource was not found."));
        }
        $deleter = $factory->createDeleter($package, $resource, $RESTparameters);
        $deleter->delete();

        //Clear the documentation in our cache for it has changed
        $this->clearCachedDocumentation();
    }

    /**
     * Deletes all Resources in a package
     * @param string $package The packagename that needs to be deleted.
     */
    public function deletePackage($package) {
        $resourceDoc = $this->getAllDoc();
        $packageDoc = $this->getAllPackagesDoc();
        if (isset($packageDoc->$package)) {
            $packageId = DBQueries::getPackageId($package);
            $subpackages = DBQueries::getAllSubpackages($packageId["id"]);

            foreach ($subpackages as $subpackage) {
                $subpackage = $subpackage["full_package_name"];
                if (isset($resourceDoc->$subpackage)) {
                    $resources = $resourceDoc->$subpackage;
                    foreach ($resourceDoc->$subpackage as $resource => $documentation) {
                        if ($resource != "creation_date") {
                            $this->deleteResource($subpackage, $resource, array());
                        }
                    }
                }
                $this->deletePackage($subpackage);
            }
            DBQueries::deletePackage($package);
        } else {
            throw new TDTException(404, array($package));
        }
    }

    /**
     * Uses a visitor to get all docs and return them
     * To have an idea what's in here, just check yourinstallationfolder/TDTInfo/Resources
     * @return a doc object containing all the packages, resources and further documentation
     */
    public function getAllDoc() {
        $doc = new Doc();
        return $doc->visitAll($this->factories);
    }

    public function getAllDescriptionDoc() {
        $doc = new Doc();
        return $doc->visitAllDescriptions($this->factories);
    }

    public function getAllAdminDoc() {
        $doc = new Doc();
        return $doc->visitAllAdmin($this->factories);
    }

    public function getAllPackagesDoc() {
        $doc = new Doc();
        return $doc->visitAllPackages();
    }

    /**
     * This function processes a resourcepackage-string
     * It will analyze it trying to do the following:
     * Find the first package-name hit, it will continue to eat pieces
     * of the resourcepackage string, untill it finds that the eaten string matches a package name
     * the piece after it found the package will be the resourcename ( if any pieces left ofcourse )
     * the pieces after the resourcename are the RESTparameters
     * @return array First entry is the [packagename], second entry is the [resourcename], third is the array with [RESTparameters]
      If the package hasn't been found FALSE is returned!
     */
    public function processPackageResourceString($packageresourcestring) {
        $result = array();

        $pieces = explode("/", $packageresourcestring);
        if (count($pieces) == 0) {
            array_push($pieces, $packageresourcestring);
        }

        $package = array_shift($pieces);

        //Get an instance of our resourcesmodel
        $model = ResourcesModel::getInstance();
        $doc = $model->getAllDoc();
        $foundPackage = FALSE;

        /**
         * Since we do not know where the package/resource/requiredparameters end, we're going to build the package string
         * and check if it exists, if so we have our packagestring. Why is this always correct ? Take a look at the
         * ResourcesModel class -> funcion isResourceValid()
         */
        $resourcename = "";
        $reqparamsstring = "";

        if (!isset($doc->$package)) {
            while (!empty($pieces)) {
                $package .= "/" . array_shift($pieces);
                if (isset($doc->$package)) {
                    $foundPackage = TRUE;
                    $resourcename = array_shift($pieces);
                    $reqparamsstring = implode("/", $pieces);
                }
            }
        } else {
            $foundPackage = TRUE;
            $resourceNotFound = TRUE;
            while (!empty($pieces) && $resourceNotFound) {
                $resourcename = array_shift($pieces);
                if (!isset($doc->$package->$resourcename) && $resourcename != NULL) {
                    $package .= "/" . $resourcename;
                    $resourcename = "";
                } else {
                    $resourceNotFound = FALSE;
                }
            }
            $reqparamsstring = implode("/", $pieces);
        }


        $RESTparameters = array();
        $RESTparameters = explode("/", $reqparamsstring);
        if ($RESTparameters[0] == "") {
            $RESTparameters = array();
        }

        if ($resourcename == "") {
            $packageDoc = $model->getAllPackagesDoc();
            $allPackages = array_keys(get_object_vars($packageDoc));

            $foundPackage = in_array($package, $allPackages);

            if (!$foundPackage) {
                throw new TDTException(404, array($packageresourcestring));
            }
        }

        $result["packagename"] = $package;
        $result["resourcename"] = $resourcename;
        $result["RESTparameters"] = $RESTparameters;
        return $result;
    }

    /**
     * Check if the resource implements iFilter or not
     * return FALSE if not the resource doesn't implement iFitler
     * return the resource if it does
     */
    public function isResourceIFilter($package, $resource) {
        foreach ($this->factories as $factory) {

            if ($factory->hasResource($package, $resource)) {

                // remote resource just proxies the url so we don't need to take that into account
                if (get_class($factory) == "GenericResourceFactory") {

                    $genericResource = new GenericResource($package, $resource);
                    $strategy = $genericResource->getStrategy();

                    $interfaces = class_implements($strategy);

                    if (in_array("iFilter", $interfaces)) {
                        return $genericResource;
                    } else {
                        return FALSE;
                    }
                } elseif (get_class($factory) == "InstalledResourceFactory") {

                    $reader = $factory->createReader($package, $resource, array(), array());
                    $interfaces = class_implements($reader);

                    if (in_array("iFilter", $interfaces)) {
                        return $reader;
                    } else {
                        return FALSE;
                    }
                }
            }
        }
    }

    /**
     * Read the resource but by calling the readAndProcessQuery function
     */
    public function readResourceWithFilter(UniversalFilterNode $query, $resource) {
        $result = $resource->readAndProcessQuery($query);
        return $result;
    }

    /**
     * get the columns from a resource
     */
    public function getColumnsFromResource($package, $resource) {
        $gen_resource_id = DBQueries::getGenericResourceId($package, $resource);

        if (isset($gen_resource_id["gen_resource_id"]) && $gen_resource_id["gen_resource_id"] != "") {
            return DBQueries::getPublishedColumns($gen_resource_id["gen_resource_id"]);
        }
        return NULL;
    }

}

?>
