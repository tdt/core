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

class LD extends SPARQL {

    public function read(&$configObject, $package, $resource) {

        $requestURI = \tdt\core\utility\RequestURI::getInstance();

        $uri = $requestURI->getRealWorldObjectURI();
        
        //$base_uri = implode("/",array($requestURI->getHostName(),$requestURI->getPackage(),$requestURI->getResource()));
        //a lot of rewriting uri mumbo jumbo and adding LDP implementation (we need to be able to manipulate an rdf model here...)
        $configObject->query = "CONSTRUCT { ?s ?p ?o } ";
        $configObject->query .= "WHERE { ?s ?p ?o . ";
        $configObject->query .= "FILTER (?s LIKE '$uri%') ";
        $configObject->query .= "}";

        return parent::read($configObject, $package, $resource);
    }

    public function isValid($package_id, $generic_resource_id) {
//        $uri = $this->endpoint . '?query=' . urlencode($this->query) . '&format=' . urlencode("application/json");
//        $data = \tdt\core\utility\Request::http($uri);
//        $result = json_decode($data->data);
//        if (!$result) {
//            $exception_config = array();
//            $exception_config["log_dir"] = Config::get("general", "logging", "path");
//            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
//            throw new TDTException(500, array("Could not transform the json data from " . $uri . " to a php object model, please check if the json is valid."), $exception_config);
//        }
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
        );
    }

}