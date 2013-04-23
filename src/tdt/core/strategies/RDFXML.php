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

class RDFXML extends AResourceStrategy {

    public function read(&$configObject, $package, $resource) {
        $parser = \ARC2::getRDFXMLParser();
        //$data = \tdt\core\utility\Request::http($configObject->uri);
        $data = $this->execRequest($configObject->uri, $configObject->endpoint_user, $configObject->endpoint_password);
        $parser->parse("",$data);
        //$parser->parse("",$data->data);

        return $parser;
    }
    
    private function execRequest($uri, $usr = "", $pass = "") {

        // is curl installed?
        if (!function_exists('curl_init')) {
            throw new \Exception('CURL is not installed!');
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

        if (!$response)
        {
            echo "endpoint returned error: " . curl_error($ch) . " - ";
            throw new \Exception("Endpoint returned an error!");
        }
        
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response_code != "200")
        {
            echo "query failed: " . $response_code . "\n" . $response . "\n";
            throw new \Exception("Query failed: $response");
        }


        curl_close($ch);

        return $response;
    }

    public function isValid($package_id, $generic_resource_id) {
        $parser = \ARC2::getRDFXMLParser();
        $parser->parse($this->uri);
        
        if (!$parser)
            throw new TDTException(500, array("Could not transform the RDF/XML data from " . $this->uri . " to a ARC model, please check if the RDF/XML is valid."), $exception_config);
        
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