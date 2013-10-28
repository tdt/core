<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * SPARQL Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class SPARQLController extends ADataController {

    public function readData($source_definition, $parameters = null){

        list($limit, $offset) = $this->calculateLimitAndOffset();

        // Retrieve the necessary variables to read from a SPARQL endpoint
        $uri = \Request::url();

        $endpoint = $source_definition->endpoint;
        $endpoint_user = $source_definition->endpoint_user;
        $endpoint_password = $source_definition->endpoint_password;
        $query = $source_definition->query;

        // Create a count query for paging purposes, this assumes that a where clause is included in the query
        // Note that the where "clause" is obligatory but it's not mandatory it is preceded by a WHERE keyword
        $matches = array();
        $keyword = "";

        // TODO provide a SPARQL query validator

        // If a select statement has been passed, we ask for JSON results
        // If a construct statement has been passed, we ask for RDF/XML
        if(stripos($query,"select") === 0){ // SELECT query
            $keyword = "select";
        }elseif(stripos($query,"construct") === 0){ // CONSTRUCT query
            $keyword = "construct";
        }else{ // No valid SPARQL keyword has been found.
            \App::abort(452, "No CONSTRUCT or SELECT statement has been found in the given query: $query");
        }

        // Prepare the count query for paging purposes
        $query = preg_replace("/($keyword\s*{.*?})/i",'',$query);

        if(stripos($query,"where") === FALSE){
            preg_match('/({.*}).*/i',$query,$matches);
        }else{
            preg_match('/(where\s*{.*}).*/i',$query,$matches);
        }

        if(count($matches) < 2){
            \App::abort(452, "Failed to retrieve the where clause from the query: $query");
        }

        $query = $matches[1];

        // Prepare the query to count results
        $count_query = 'SELECT count(?s) AS ?count ' . $query;

        $count_query = urlencode($count_query);
        $count_query = str_replace("+", "%20", $count_query);

        $count_uri = $endpoint . '?query=' . $count_query . '&format=' . urlencode("application/rdf+xml");
        $response = $this->executeUri($count_uri, $endpoint_user, $endpoint_password);

        // Parse the triple response and retrieve the form them containing our count result
        $parser = \ARC2::getRDFXMLParser();
        $parser->parse('',$response);

        $triples = $parser->triples;

        // Get the results#value, in order to get a count of all the results
        // This will be used for paging purposes
        $count = 0;
        foreach ($triples as $triple){
            if(!empty($triple['p']) && preg_match('/.*sparql-results#value/',$triple['p'])){
                $count = $triple['o'];
            }
        }

        // Calculate page link headers, previous, next and last based on the count from the previous query
        $paging = $this->calculatePagingHeaders($limit, $offset, $count);

        $query = $source_definition->query;
        if(!empty($offset)){
            $query = $query . " OFFSET $offset ";
        }

        if(!empty($limit)){
            $query = $query . " LIMIT $limit";
        }

        // Prepare the query with proper encoding for the request
        $q = urlencode($query);
        $q = str_replace("+", "%20", $q);

        if($keyword == 'select'){

            $query_uri = $endpoint . '?query=' . $q . '&format=' . urlencode("application/sparql-results+json");
            $response = $this->executeUri($query_uri, $endpoint_user, $endpoint_password);
            $result = json_decode($response);

            if(!$result){
                \App::abort(452, 'The query has been executed, but failed to return proper JSON');
            }

            $is_semantic = false;

        }else{

            $query_uri = $endpoint . '?query=' . $q . '&format=' . urlencode("application/rdf+xml");

            $response = $this->executeUri($query_uri, $endpoint_user, $endpoint_password);

            // Parse the triple response and retrieve the triples from them
            $result = \ARC2::getRDFXMLParser();
            $result->parse('', $response);
            $is_semantic = true;
        }

        $data = new Data();
        $data->data = $result;
        $data->paging = $paging;
        $data->is_semantic = $is_semantic;

        return $data;
    }

    /**
     * Execute a query using cURL and return the result.
     * This function will abort upon error.
     */
    private function executeUri($uri, $user = '', $password = ''){

        // Check if curl is installed on this machine
        if (!function_exists('curl_init')) {
           \App::abort(500, "cURL is not installed as an executable on this server, this is necessary to execute the SPARQL query properly.");
        }

        // Initiate the curl statement
        $ch = curl_init();

        // If credentials are given, put the HTTP auth header in the cURL request
        if(!empty($user)){
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $password);
        }

        // Set the request uri
        curl_setopt($ch, CURLOPT_URL, $uri);

        // Request for a string result instead of having the result being outputted
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);

        if (!$response){
            $curl_err = curl_error($ch);
            \App::abort(500, "Something went wrong while executhing query. The request we put together was: $uri.");
        }

        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // According to the SPARQL 1.1 spec, a SPARQL endpoint can only return 200,400,500 reponses
        if($response_code == '400'){
            \App::abort(452, "The SPARQL endpoint returned a 400 error.");
        }else if($response_code == '500'){
            \App::abort(452, "The SPARQL endpoint returned a 500 error.");
        }
        curl_close($ch);

        return $response;
    }
}
