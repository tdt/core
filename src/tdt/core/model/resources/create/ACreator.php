<?php

/**
 * AClass for creating a resource
 *
 * @package The-Datatank/model/resources/create
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model\resources\create;

use tdt\core\model\DBQueries;
use RedBean_Facade as R;

abstract class ACreator {

    public function __construct($package, $resource) {
        $this->package = $package;
        $this->resource = $resource;
    }

    /**
     * set parameters, we leave this to the subclassx
     */
    abstract public function setParameter($key, $value);

    /**
     * Creates a resource
     */
    abstract public function create();

    /**
     * get all the parameters to create a resource
     * @return hash with key = parameter name and value = documentation about the parameter
     */
    public function documentParameters() {
        $createParameters = array("resource_type" => "The type of the resource. i.e. generic, installed,remote");
        return array_merge($createParameters, $this->documentMetaDataParameters());
    }

    public function documentMetaDataParameters() {
        return array(
            "package_title" => "An alias for the package name, used for presentation and visualization purposes.",
            "resource_title" => "An alias for the resource name, used for presentation and visualization purposes.",
            "tags" => "A serie of descriptive tags, separated with a semi-colon.",
            "audience" => "A class of entity for whom the resource is intended or useful.",
            "contributor" => "An entity responsible for making contributions to the resource.",
            "coverage" => "The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant.",
            "creator" => "An entity primarily responsible for making the resource.",
            "language" => "The language of the resource.",
            "license" => "The license of the data.",
            "publisher" => "An entity responsible for making the resource available.",
            "rights" => "Information about rights held in and over the resource.",
            "rightsHolder" => "A person or organization owning or managing rights over the resource.",
            "example_uri" => "An example of how a call to the resource may look like."
        );
    }

    /**
     * get the required parameters
     * @return array with all of the required parameters
     */
    public function documentRequiredParameters() {
        return array("resource_type");
    }

    /**
     * make package id
     * @return id of the package
     */
    protected function makePackage($package) {
        /**
         * split the package string in its package and subpackage components
         * check for every package and subpackage if they exist, if not create them
         */
        $full_package_name = $package;
        $packagepieces = explode("/", $package);
        $package = array_shift($packagepieces);

        // the top level package id
        $parentId = "";

        /**
         * Create main package (top level package)
         */
        $result = DBQueries::getPackageId($full_package_name);

        if (sizeof($result) == 0) {
            $parentId = DBQueries::storePackage($package, $package, NULL);
        } else {
            $parentId = $result["id"];
        }

        /*
         * build up the full_package_name from square while adding the subpackages
         */
        $fullPackageName = $package;

        /**
         * create package entries for every subpackage
         */
        foreach ($packagepieces as $subpackage) {
            $result = DBQueries::getPackageIdByParentId($subpackage, $parentId);

            $fullPackageName = $fullPackageName . "/" . $subpackage;

            if (sizeof($result) == 0) {
                $parentId = DBQueries::storePackage($subpackage, $fullPackageName, $parentId);
            } else {
                $parentId = $result[0]["id"];
            }
        }
        return $parentId;
    }

    /**
     * make resource id
     * @return id of the resource
     */
    protected function makeResource($package_id, $resource, $resource_type) {

        $checkExistence = DBQueries::getResourceIdByPackageId($resource, $package_id);

        if (sizeof($checkExistence) == 0) {
            return DBQueries::storeResource($package_id, $resource, $resource_type);
        }
        return $checkExistence[0]["id"];
    }

}

?>
