<?php

/**
 * This class OntologyUpdater updates ontologgy's by adding mappings
 * When updating an ontology, we always expect a POST method!
 *
 * @package The-Datatank/model/resources/update
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

class OntologyUpdater extends AUpdater {

    private $params = array();

    public function __construct($package, $resource, $RESTparameters) {
        parent::__construct($package, $resource, $RESTparameters);
    }

    public function getParameters() {
        return array(
            "update_type" => "...",
            "method" => "The method preferred or add",
            "value" => "Supplied value for the specified method.",
            "namespace" => "The namespace of the value",
            "prefix" => "Possible prefix to add to the namespace",
            "REST" => "temp rest var"
        );
    }

    public function getRequiredParameters() {
        return array("update_type", "method", "value", "namespace");
    }

    public function getDocumentation() {
        return "This class will update the package ontology";
    }

    protected function setParameter($key, $value) {
        $this->params[$key] = $value;
    }

    public function update() {
        /* TDTAdmin/Ontoloy is now trimmed, this code is not correct anymore
         * if ($this->resource !== "Ontology")
          throw new OntologyUpdateTDTException("Update only allowed on the resource TDTAdmin/Ontology");

          //First RESTparameters is the package, rest is the Resource path
          $package = array_shift($this->RESTparameters);
         * 
         */

        //Resource path empty? 
        if (count($this->RESTparameters) == 0)
            throw new TDTException(500,array("OntologyUpdater - Cannot update the ontology of a package, please specify a resource"));

        //Assemble path
        $path = $this->resource.'/'. implode('/', $this->RESTparameters);

        if (!isset($this->params['method']))
            throw new TDTException(500,array("OntologyUpdater - 'Method parameter is not set!'"));

        if (!isset($this->params['value']))
            throw new TDTException(500, array('OntologyUpdater - Value parameter is not set!'));

        if (!isset($this->params['namespace']))
            throw new TDTException(500,array('OntologyUpdater - Namespace parameter is not set!'));

        if (!isset($this->params['namespace']))
            $this->params['prefix'] = null;
        
        //Do we want to add a mapping, or do we want to set the mapping we prefer to the others
        switch ($this->params['method']) {
            case 'add_map':
                OntologyProcessor::getInstance()->updatePathMap($this->package, $path, $this->params['value'], $this->params['namespace'], $this->params['prefix']);
                break;

            case 'prefer_map':
                OntologyProcessor::getInstance()->updatePathPreferredMap($this->package, $path, $this->params['value'], $this->params['namespace']);
                break;

            case 'delete_map':
                OntologyProcessor::getInstance()->updatePathDeleteMap($this->package, $path, $this->params['value'], $this->params['namespace']);
                break;

            default:
                throw new TDTException(500,array('Method ' . $this->params['method'] . ' does not exist!'));
        }
    }

}

?>