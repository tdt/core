<?php

/**
 * This will get a resource description from the databank and add the right strategy to process the call to the GenericResource class
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\model;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\create\GenericResourceCreator;
use tdt\core\model\resources\delete\GenericResourceDeleter;
use tdt\core\model\resources\GenericResource;
use tdt\core\model\resources\read\GenericResourceReader;
use tdt\core\model\resources\update\GenericResourceUpdater;
use tdt\exceptions\TDTException;

class GenericResourceFactory extends AResourceFactory {

    public function __construct() {
        
    }

    public function hasResource($package, $resource) {
        $resource = DBQueries::hasGenericResource($package, $resource);
        return isset($resource["present"]) && $resource["present"] >= 1;
    }

    public function createCreator($package, $resource, $parameters, $RESTparameters) {
        if (!isset($parameters["generic_type"])) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array("The generic type has not been set"), $exception_config);
        }
        $creator = new GenericResourceCreator($package, $resource, $RESTparameters, $parameters["generic_type"]);
        foreach ($parameters as $key => $value) {
            $creator->setParameter($key, $value);
        }
        return $creator;
    }

    public function createReader($package, $resource, $parameters, $RESTparameters) {
        $reader = new GenericResourceReader($package, $resource, $RESTparameters);
        $reader->processParameters($parameters);
        return $reader;
    }

    public function createDeleter($package, $resource, $RESTparameters) {
        $deleter = new GenericResourceDeleter($package, $resource, $RESTparameters);
        return $deleter;
    }

    public function makeDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }

            foreach ($resourcenames as $resourcename) {
                $documentation = DBQueries::getGenericResourceDoc($package, $resourcename);
                $example_uri = DBQueries::getExampleUri($package, $resourcename);

                if ($example_uri == FALSE) {
                    $example_uri = "";
                }


                $doc->$package->$resourcename = new \stdClass();
                $doc->$package->$resourcename->documentation = $documentation["doc"];
                $doc->$package->$resourcename->example_uri = $example_uri;
                /**
                 * Create a generic resource, get the strategy and ask for
                 * the read parameters of the strategy.
                 * NOTE: We don't ask for generic resource parameters, because there are none !
                 */
                $genres = new GenericResource($package, $resourcename);
                $strategy = $genres->getStrategy();
                $doc->$package->$resourcename->parameters = $strategy->documentReadParameters();
                $doc->$package->$resourcename->requiredparameters = array();
            }
        }
    }

    public function makeDescriptionDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }

            foreach ($resourcenames as $resourcename) {
                $documentation = DBQueries::getGenericResourceDoc($package, $resourcename);
                $doc->$package->$resourcename = new \stdClass();
                $doc->$package->$resourcename->documentation = $documentation["doc"];
                $doc->$package->$resourcename->generic_type = $documentation["type"];
                $doc->$package->$resourcename->resource_type = "generic";
                /**
                 * Get the strategy properties
                 */
                $genericId = $documentation["id"];
                $strategyTable = "generic_resource_" . strtolower($documentation["type"]);

                $result = DBQueries::getStrategyProperties($genericId, $strategyTable);
                if (isset($result[0])) {
                    foreach ($result[0] as $column => $value) {
                        if ($column != "id" && $column != "gen_resource_id") {
                            $doc->$package->$resourcename->$column = $value;
                        }
                    }
                }

                /**
                 * Get the metadata properties
                 */
                $metadata = DBQueries::getMetaData($package, $resourcename);
                if (!empty($metadata)) {
                    foreach ($metadata as $name => $value) {
                        if ($name != "id" && $name != "resource_id") {
                            $doc->$package->$resourcename->$name = $value;
                        }
                    }
                }

                /**
                 * Get the published columns
                 */
                $columns = DBQueries::getPublishedColumns($genericId);
                // pretty formatted columns
                $prettyColumns = array();
                if (!empty($columns)) {
                    foreach ($columns as $columnentry) {
                        $prettyColumns[$columnentry["index"]] = $columnentry["column_name"];
                    }
                    $doc->$package->$resourcename->columns = $prettyColumns;
                }

                /**
                 * Get the published columns aliases
                 */
                $columnAliases = array();
                if (!empty($columns)) {
                    foreach ($columns as $columnentry) {
                        $columnAliases[$columnentry["column_name"]] = $columnentry["column_name_alias"];
                    }
                    $doc->$package->$resourcename->column_aliases = $columnAliases;
                }

                $doc->$package->$resourcename->parameters = array();
                $doc->$package->$resourcename->requiredparameters = array();
            }
        }
    }

    protected function getAllResourceNames() {
        $results = DBQueries::getAllGenericResourceNames();
        $resources = array();
        foreach ($results as $result) {
            if (!array_key_exists($result["package_name"], $resources)) {
                $resources[$result["package_name"]] = array();
            }
            $resources[$result["package_name"]][] = $result["res_name"];
        }
        return $resources;
    }

    public function makeDeleteDoc($doc) {
        //add stuff to the delete attribute in doc. No other parameters expected
        $d = new \stdClass();
        if (!isset($doc->delete)) {
            $doc->delete = new \stdClass();
        }
        $d->documentation = "You can delete every generic resource by sending a DELETE HTTP request to the resource definition located in TDTAdmin/Resources.";
        $doc->delete->generic = new \stdClass();
        $doc->delete->generic = $d;
    }

    public function makeCreateDoc($doc) {
        $d = array();
        foreach ($this->getAllStrategies() as $strategy) {
            $res = new GenericResourceCreator("", "", array(), $strategy);
            $d[$strategy] = new \stdClass();
            $d[$strategy]->documentation = "When your file is structured according to a $strategy -datasource, you can perform a PUT request and load this file in this DataTank";
            $d[$strategy]->parameters = $res->documentParameters();
            $d[$strategy]->requiredparameters = $res->documentRequiredParameters();
        }
        if (!isset($doc->create)) {
            $doc->create = new \stdClass();
        }
        $doc->create->generic = new \stdClass();
        $doc->create->generic = $d;
    }

    public function makeUpdateDoc($doc) {
        $d = array();
        foreach ($this->getAllStrategies() as $strategy) {
            $res = new GenericResourceUpdater("", "", array(), $strategy);
            $d[$strategy] = new \stdClass();
            $d[$strategy]->documentation = "When your generic resource is made you can update properties of it by passing the property names via a PATCH request to TDTAdmin/Resources. Note that only create parameters are adjustable.";
            $d[$strategy]->parameters = array();
            $d[$strategy]->requiredparameters = array();
        }
        if (!isset($doc->update)) {
            $doc->update = new \stdClass();
        }
        $doc->update->generic = new \stdClass();
        $doc->update->generic = $d;
    }

    private function getAllStrategies() {
        $strategies = array();
        if ($handle = opendir(__DIR__ . '/../strategies')) {
            while (false !== ($strat = readdir($handle))) {
                //if the object read is a directory and the configuration methods file exists, then add it to the installed strategie
                if ($strat != "." && $strat != ".." && $strat != "README.md" && !is_dir(__DIR__ . "/../strategies/" . $strat) && file_exists(__DIR__ . "/../strategies/" . $strat)) {
                    $fileexplode = explode(".", $strat);
                    $classname = "tdt\\core\\strategies\\" . $fileexplode[0];
                    $class = new \ReflectionClass($classname);
                    if (!$class->isAbstract()) {
                        $strategies[] = $fileexplode[0];
                    }
                }
            }
            closedir($handle);
        }
        return $strategies;
    }

}

?>
