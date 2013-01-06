<?php

/**
 * This class OntologyCreator creates ontologies.
 * When creating an ontology, we always expect a PUT method!
 *
 * @package The-Datatank/model/resources/create
 * @copyright (C) 2011 by iRail vzw/asbl 
 * @license AGPLv3
 * @author Miel Vander Sande
 */

class OntologyCreator extends ACreator {

    private $RESTparameters;
    private $resource_type;

    function __construct($package, $resource, $RESTparameters) {
        parent::__construct($package, $resource, $RESTparameters);
        $this->RESTparameters = $RESTparameters;
    }

    public function create() {
        if (!isset($this->resource)) {
            //Create empty ontology for a package or parse the file if there is one
            if (property_exists($this, "ontology_file"))
                OntologyProcessor::getInstance()->createOntology($this->package, $this->ontology_file);
            else
                OntologyProcessor::getInstance()->createOntology($this->package);
        }else if (isset($this->resource) && count($this->RESTparameters) == 0) {
            //Create empty ontology for a package or parse the file if there is one
            if (property_exists($this, "ontology_file"))
                OntologyProcessor::getInstance()->createOntology($this->package, $this->ontology_file);
            //When the auto_generate parameter is supplied and set to true, 
            //try to auto-generate (only for this resource)
            else if (property_exists($this, "auto_generate")) {
                if ($this->auto_generate) {
                    $fields = null;
                    if (DBQueries::hasGenericResource($this->package, $this->resource)) {
                        $genres = new GenericResource($this->package, $this->resource);
                        $strategy = $genres->getStrategy();
                        $fields = $strategy->getFields($this->package, $this->resource);
                    }
                    OntologyProcessor::getInstance()->generateOntology($this->package, $this->resource, $fields);
                }
            }else
                OntologyProcessor::getInstance()->createOntology($this->package);
            //Add class entry for resource in empty resource
            OntologyProcessor::getInstance()->createClassPath($this->package, $this->resource);
        } else {
            //Create ontology for a package and describe the given path
            if (!isset($this->resource_type))
                throw new TDTException(452,array("Ontology: Type of resource is not specified"));
            //turn REST parameters into the classpath of the resource
            $resource = $this->resource . '/' . implode("/", $this->RESTparameters);

            //Check the PUT parameter type to 
            if ($this->resource_type == "property")
                OntologyProcessor::getInstance()->createPropertyPath($this->package, $resource);
            else if ($this->resource_type == "class")
                OntologyProcessor::getInstance()->createClassPath($this->package, $resource);
            else
                throw new TDTException(452,array("Ontology: Type of resource is not correct"));
        }
    }

    public function setParameter($key, $value) {
        if ($key == "type")
            $this->resource_type = $value;
        else if ($key == "ontology_file")
            $this->ontology_file = $value;
        else if ($key == "auto_generate")
            $this->auto_generate = $value;
    }

    public function documentParameters() {
        $parameters = parent::documentParameters();
        $parameters["package"] = "The package for which you want to create an ontology";
        $parameters["resource"] = "The resource for which you want to create a description";
        $parameters = array_merge($parameters, $this->strategy->documentCreateParameters());
        return $parameters;
    }

    public function documentRequiredParameters() {
        $parameters = parent::documentRequiredParameters();
        $parameters[] = "package";
        $parameters = array_merge($parameters, $this->strategy->documentCreateRequiredParameters());
        return $parameters;
    }

}

?>
