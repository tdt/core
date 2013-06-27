<?php

/**
 * This class handles Linked Data Resources
 *
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 * @author Pieter Colpaert
 */

namespace tdt\core\strategies;
use tdt\core\model\resources\AResourceStrategy;
use RedBean_Facade as R;

class LD extends SPARQL {

    public function read(&$configObject, $package, $resource) {

        $requestURI = \tdt\core\utility\RequestURI::getInstance();

        $uri = $requestURI->getRealWorldObjectURI();

        //Get graph to query from URI - Not needed anymore?
//        $graph = substr($uri, 0, stripos($uri, $package . "/" .$resource) + strlen($package . "/" .$resource));
//        $otherpart = substr($uri, stripos($uri, $package . "/" .$resource) + strlen($package . "/" .$resource));
//        $resultgraph = R::getRow( 'select graph_name from graph WHERE graph_name like :graph COLLATE utf8_general_ci limit 1', array(':graph' => $graph) );
//        $graph = $resultgraph["graph_name"];
//        $uri = $graph . $otherpart;

        $configObject->query = "CONSTRUCT { ?s ?p ?o } ";
        $configObject->query .= "WHERE { ";
        $configObject->query .= "?s ?p ?o .";
        $configObject->query .= "FILTER ( (?s LIKE '$uri') OR (?s LIKE '$uri/%') )";
        $configObject->query .= "} ORDER BY asc(?s) ";
        $configObject->req_uri = $uri;

        return parent::read($configObject, $package, $resource);
    }

    public function isValid($package_id, $generic_resource_id) {
        return true;
    }

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("endpoint");
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
            "endpoint_user" => "Username for file behind authentication",
            "endpoint_password" => "Password for file behind authentication"
        );
    }

    /**
     * Returns an array with parameter => documentation pairs that can be used to read a CSV resource.
     * @return array with parameter => documentation pairs
     */
    public function documentReadParameters() {
        $page_size = AResourceStrategy::$DEFAULT_PAGE_SIZE;
        return array(
            "page" => "Represents the page number if the dataset is paged, this parameter works together with page_size, which is default set to $page_size. Set this parameter to -1 if you don't want paging to be applied.",
            "page_size" => "Represents the size of a page, this means that by setting this parameter, you can alter the amount of results that are returned, in one page (e.g. page=1&page_size=3 will give you results 1,2 and 3).",
            "limit" => "Instead of page/page_size you can use limit and offset. Limit has the same purpose as page_size, namely putting a cap on the amount of entries returned, the default is $page_size. Set this parameter to -1 if don't want paging to be applied.",
            "offset" => "Represents the offset from which results are returned (e.g. ?offset=12&limit=5 will return 5 results starting from 12).",
        );
    }
}