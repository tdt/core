<?php

/**
 * This class handles a SPARQL query
 *
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 * @author Pieter Colpaert
 */

namespace tdt\core\strategies;

use tdt\framework\TDTException;

class SPARQL extends JSON {


    public function read(&$configObject,$package,$resource){
        
        $this->uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("application/json");
        parent::read($configObject,$package,$resource);
    }
    
    public function isValid($package_id,$generic_resource_id){
        $this->uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("application/json");    
        parent::isValid($package_id,$generic_resource_id);
    }
    

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("endpoint", "query");
    }

    /**
     * The parameters ( array keys ) returned all of the parameters that can be used to create a strategy.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateParameters() {
        $this->parameters["endpoint"] = "The URI of the SPARQL endpoint.";
        $this->parameters["query"] = "The SPARQL query";
        return $this->parameters;
    }
}
