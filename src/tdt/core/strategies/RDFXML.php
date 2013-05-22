<?php

/**
 * This class handles Turtle input
 *
 * @copyright (C) 2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Miel Vander Sande
 */

namespace tdt\core\strategies;
use tdt\exceptions\TDTException;
use tdt\core\model\resources\AResourceStrategy;
use tdt\core\utility\Config;

class RDFXML extends AResourceStrategy {

    public function read(&$configObject, $package, $resource) {

        $this->package = $package;
        $this->resource = $resource;

        // Virtuoso only allows for 10000 rows to sort, so we have to limit the limit.
        // Note that limit and offset are applied after sorting(!!) which means that if
        // the limit+offset are more than 10K, the sort will fail.
        if($this->limit + $this->offset > 10000){
            $this->limit = 10000 - $this->offset;
            $this->page_size = 10000 - $this->offset;
        }

        $this->calculateLimitAndOffset();

        $parser = \ARC2::getRDFXMLParser();
        $data = $this->execRequest($configObject->uri, $configObject->endpoint_user, $configObject->endpoint_password);
        $parser->parse("",$data);

        return $parser;
    }

    private function execRequest($uri, $usr = "", $pass = "") {

        // Is curl installed?
        if (!function_exists('curl_init')) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array('CURL is not installed!'), $exception_config);
        }

        // get curl handle
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $usr . ":" . $pass);

        // set request url
        curl_setopt($ch, CURLOPT_URL, $uri);

        // return response, don't print/echo
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $response = curl_exec($ch);

        if (!$response){
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("The call to the SPARQL endpoint returned an error: curl_error($ch)"), $exception_config);
        }

        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response_code != "200"){ // According to the SPARQL 1.1 spec, it can only return 200,400,500 reponses.

            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";

            if($response_code == "400"){
                throw new TDTException(452, array("The SPARQL query failed, with the message: $response."), $exception_config);
            }else{
                throw new TDTException(500, array("The SPARQL query failed, with response code: $response_code and message: $response."), $exception_config);
            }
        }


        curl_close($ch);

        return $response;
    }

    public function isValid($package_id, $generic_resource_id) {
        $parser = \ARC2::getRDFXMLParser();
        $parser->parse($this->uri);

        if (!$parser){
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(500, array("Could not transform the RDF/XML data from " . $this->uri . " to a ARC model, please check if the RDF/XML is valid."), $exception_config);
        }

        return true;
    }

    /**
     * The parameters returned are required to make this strategy work.
     * @return array with parameter => documentation pairs
     */
    public function documentCreateRequiredParameters() {
        return array("uri");
    }

    public function documentReadRequiredParameters() {
        return array();
    }

    public function documentUpdateRequiredParameters() {
        return array();
    }

    public function documentCreateParameters() {
        return array(
            "uri" => "The URI of the RDF/XML file.",
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