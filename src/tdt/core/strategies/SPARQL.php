<?php

/**
 * This class handles Turtle input
 *
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

namespace tdt\core\strategies;

class SPARQL extends RDFXML {

    public function read(&$configObject, $package, $resource) {
        $param = get_object_vars($this);

        foreach ($param as $key => $value) {
            //$value = addslashes($value);
            $configObject->query = preg_replace("/(.*)(\\?$key)(\\s.*)/", "$1$value$3", $configObject->query);
        }
        $this->uri = $this->endpoint . '?query=' . urlencode($configObject->query) . '&format=' . urlencode("application/rdf+xml");
        
        return parent::read($configObject, $package, $resource);
    }

    public function isValid($package_id, $generic_resource_id) {
        $this->uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("application/rdf+xml");
        
        parent::isValid($package_id, $generic_resource_id);
    }

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("endpoint", "query");
    }

    public function documentReadRequiredParameters() {
        return array();
    }

    public function documentUpdateRequiredParameters() {
        return array();
    }

    public function documentCreateParameters() {
        return array(
            "endpoint" => "The URI of the SPARQL endpoint.",
            "query" => "The SPARQL query"
        );
    }

    public function documentReadParameters() {
        return array();
    }

    public function documentUpdateParameters() {
        return array();
    }

    public function getFields($package, $resource) {
        return array();
    }

}