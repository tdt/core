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
        unset($param["rest_params"]);
        foreach ($param as $key => $value) {
            $value = addslashes($value);
            $configObject->query = str_replace("\$\{$key\}", "\"$value\"", $configObject->query);
        }

        /* configuration */
//        $config = array(
//            /* remote endpoint */
//            'remote_store_endpoint' => $configObject->endpoint,
//        );

        /* instantiation */
        //$store = \ARC2::getRemoteStore($config);

        $matches = array();
        preg_match_all("/GRAPH\s*?<(.*?)>/", $configObject->query, $matches, PREG_SET_ORDER);


        foreach ($matches as $match) {
            $graph = $match[1];
            $replace = \tdt\core\model\DBQueries::getLatestGraph($graph);
            
            if ($replace)
                $configObject->query = str_replace("GRAPH <$graph>", "GRAPH <$replace>", $configObject->query);
    
        }


        $q = urlencode($configObject->query);
        $q = str_replace("+", "%20", $q);

        $configObject->uri = $configObject->endpoint . '?query=' . $q . '&format=' . urlencode("application/rdf+xml");

        return parent::read($configObject, $package, $resource);
        //$rows = $store->query($configObject->query);
        //return $rows;
    }

    public function isValid($package_id, $generic_resource_id) {
        $this->uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("application/rdf+xml");

        /* parser instantiation */
        $parser = \ARC2::getSPARQLParser();

        $parser->parse($this->query);
        if ($parser->getErrors())
            throw new TDTException(400, array("SPARQL Query could not be parsed."), $exception_config);

        return parent::isValid($package_id, $generic_resource_id);
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
            "query" => "The SPARQL query",
            "endpoint_user" => "Username for file behind authentication",
            "endpoint_password" => "Password for file behind authentication"
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