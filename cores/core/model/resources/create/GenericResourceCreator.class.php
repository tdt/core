<?php

/**
 * This class creates a generic resources. When creating a resource, we always expect a PUT method!
 *
 * @package The-Datatank/model/resources/create
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 * @author Pieter Colpaert
 */

class GenericResourceCreator extends ACreator {

    private $strategy;

    public function __construct($package, $resource, $RESTparameters, $generic_type) {
        parent::__construct($package, $resource, $RESTparameters);
        // Add the parameters of the strategy!
        $this->generic_type = $generic_type;
        if (!file_exists(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/strategies/" . $this->generic_type . ".class.php")) {
            throw new TDTException(452,array("Generic type does not exist: " . $this->generic_type . "."));
        }
        include_once(Config::get("general", "homedir") . Config::get("general","subdir") ."cores/core/custom/strategies/" . $this->generic_type . ".class.php");
        // add all the parameters to the $parameters
        // and all of the requiredParameters to the $requiredParameters
        $this->strategy = new $this->generic_type();
        $this->strategy->package = $package;
        $this->strategy->resource = $resource;
    }

    /**
     * This overrides the previous defined required parameters by ACreator. It needs $strategy to be an instance of a strategy. Therefor setParameter needs to have been called upon with a generic_type as argument.
     */
    public function documentParameters() {
        $parameters = parent::documentParameters();
        $parameters["generic_type"] = "The type of the generic resource.";
        $parameters["documentation"] = "Some descriptional documentation about the generic resource.";
        $parameters = array_merge($parameters, $this->strategy->documentCreateParameters());
        return $parameters;
    }

    /**
     * This overrides the previous defined required parameters by ACreator. It needs $strategy to be an instance of a strategy. Therefor setParameter needs to have been called upon with a generic_type as argument.
     */
    public function documentRequiredParameters() {
        $parameters = parent::documentRequiredParameters();
        $parameters[] = "documentation";
        $parameters[] = "generic_type";
        $parameters = array_merge($parameters, $this->strategy->documentCreateRequiredParameters());
        return $parameters;
    }

    public function setParameter($key, $value) {
        // set the correct parameters, to the this class or the strategy we're sure that every key,value passed is correct
        $this->$key = $value;
        if (isset($this->strategy)) {
            $this->strategy->$key = $value;
        }
    }

    /**
     * execution method
     * Preconditions: 
     * parameters have already been set.
     */
    public function create() {
        R::setStrictTyping( false );
        /*
         * Create the package and resource entities and create a generic resource entry.
         * Then pick the correct strategy, and pass along the parameters!
         */
        $package_id = parent::makePackage($this->package);
        
        $resource_id = parent::makeResource($package_id, $this->resource, "generic");

        $meta_data_id = DBQueries::storeMetaData($resource_id, $this, array_keys(parent::documentMetaDataParameters()));

        $generic_id = DBQueries::storeGenericResource($resource_id, $this->generic_type, $this->documentation);
        try {
            $this->strategy->onAdd($package_id, $generic_id);
        } catch (Exception $ex) {

            // delete metadata about the resource
            DBQueries::deleteMetaData($this->package, $this->resource);

            //now the only thing left to delete is the main row
            DBQueries::deleteGenericResource($this->package, $this->resource);

            // also delete the resource entry
            DBQueries::deleteResource($this->package, $this->resource);
            
            throw new Exception($ex->getMessage());
        }
    }

}

?>