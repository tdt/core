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

use tdt\framework\Log;
use tdt\framework\TDTException;

class SPARQL extends CSV {

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("endpoint", "query");
    }

    /**
     * @deprecated
     */
    public function documentUpdateParameters() {
        $this->parameters["endpoint"] = "The URI of the SPARQL endpoint.";
        $this->parameters["query"] = "The SPARQL query";
        $this->parameters["PK"] = "The primary key of an entry. This must be the name of an existing column name in the SPARQL resultset.";
        $this->parameters["start_row"] = "The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.";
        return $this->parameters;
    }

    /**
     * The parameters ( array keys ) returned all of the parameters that can be used to create a strategy.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateParameters() {
        $this->parameters["endpoint"] = "The URI of the SPARQL endpoint.";
        $this->parameters["query"] = "The SPARQL query";
        $this->parameters["PK"] = "The primary key of an entry. This must be the name of an existing column name in the SPARQL resultset.";
        $this->parameters["start_row"] = "The number of the row (rows start at number 1) at which the actual data starts; i.e. if the first two lines are comment lines, your start_row should be 3. Default is 1.";
        return $this->parameters;
    }

    /**
     * Read a resource
     * @param $configObject The configuration object containing all of the parameters necessary to read the resource.
     * @param $package The package name of the resource
     * @param $resource The resource name of the resource
     * @return $mixed An object created with fields of the query result.
     */
    public function read(&$configObject, $package, $resource) {
        /**
         * check if the endpoint is valid ( not empty )
         */
//        if (!isset($configObject->endpoint))
//            throw new TDTException(452, array("Can't find endpoint for executing SPARQL"));
//
//        if (!isset($configObject->query))
//            throw new TDTException(452, array("No SPARQL query supplied"));
//
//        $configObject->uri = $configObject->endpoint . '?query=' . urlencode($configObject->query) . '&format=csv';
//        $configObject->has_header_row = "1";
//        $configObject->delimiter = ",";
//        $configObject->start_row = "1";

        return parent::read($configObject, $package, $resource);
    }

    public function onAdd($package_id, $gen_resource_id) {
        if (!isset($this->uri)) {
            $this->uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("text/csv");
        }
        if (!isset($this->delimiter)) {
            $this->delimiter = ",";
        }
        parent::onAdd($package_id, $gen_resource_id);
    }

}

?>
