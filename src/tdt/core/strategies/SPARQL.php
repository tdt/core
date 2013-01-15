<?php

/**
 * This class handles a SPARQL query
 *
 * @package The-Datatank/custom/strategies
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
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

    public function onAdd($package_id, $gen_resource_id) {
        if(!empty($this->endpoint) && !empty($this->query)){
            $this->uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("application/json");
        }

        parent::onAdd($package_id, $gen_resource_id);
    }

}

?>
