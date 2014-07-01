<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;
use Tdt\Core\Pager;
use Tdt\Core\Repositories\Interfaces\OntologyRepositoryInterface;

/**
 * SPARQL Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class SPARQLController extends ADataController
{
    private $ontologies;
    private static $MAX_LIMIT = 10000; // Default max rows returned in Virtuoso

    public function __construct(OntologyRepositoryInterface $ontologies)
    {
        $this->ontologies = $ontologies;
    }

    public function readData($source_definition, $rest_parameters = array())
    {
        list($limit, $offset) = Pager::calculateLimitAndOffset();

        // Sparql endpoints often have a built in limit on the amount of rows that they return
        // Avoid problems by capping the given limit by the Pager class
        if ($limit > self::$MAX_LIMIT) {
            $limit = self::$MAX_LIMIT;
        }

        // Retrieve the necessary variables to read from a SPARQL endpoint
        $uri = \Request::url();

        $endpoint = $source_definition['endpoint'];
        $endpoint_user = $source_definition['endpoint_user'];
        $endpoint_password = $source_definition['endpoint_password'];
        $query = $source_definition['query'];

        // Process the parameters in the uri (to catch hashtag values for example)
        $query = $this->processParameters($query);

        // Create a count query for paging purposes, this assumes that a where clause is included in the query
        // Note that the where "clause" is obligatory but it's not mandatory it is preceded by a WHERE keyword
        $matches = array();
        $keyword = "";

        // If a select statement has been passed, we ask for JSON results
        // If a construct statement has been passed, we ask for RDF/XML
        if (stripos($query, "select") !== false) { // SELECT query
            $keyword = "select";
        } elseif (stripos($query, "construct") !== false) { // CONSTRUCT query
            $keyword = "construct";
        } else { // No valid SPARQL keyword has been found, is checked during validation
            \App::abort(500, "No CONSTRUCT or SELECT statement has been found in the given query: $query");
        }

        // Prepare the count query for paging purposes
        // This implies the removal of the select or construct statement
        // and only using the where statement

        // Make a distinction between select and construct since
        // construct will be followed by a {} sequence, whereas a select statement will not
        $prefix = '';
        $filter = '';

        // Covers FROM <...> FROM <...> WHERE{ } , FROM <...> FROM <...> { }, WHERE { }, { }
        $where_clause = '(.*?(FROM.+?{.+})|.*?(WHERE.*{.+})|.*?({.+}))[a-zA-Z0-9]*?';
        $matches = array();

        if ($keyword == 'select') {

            $regex = $keyword . $where_clause;

            preg_match_all("/(.*)$regex/msi", $query, $matches);
        } else {

            preg_match_all("/(.*)$keyword(\s*\{[^{]+\})$where_clause/mis", $query, $matches);
        }

        $prefix = $matches[1][0];
        $filter = "";

        // Preg match all has 3 entries for the where clause, pick the first hit
        if (!empty($matches[3][0])) {
            $filter = $matches[3][0];
        }

        if (!empty($matches[4][0])) {
            $filter = $matches[4][0];
        }

        if (!empty($matches[5][0])) {
            $filter = $matches[5][0];
        }

        if (empty($filter)) {
            \App::abort(500, "Failed to retrieve the where clause from the query: $query");
        }

        // Prepare the query to count results
        $count_query = $matches[1][0] . ' SELECT (count(*) AS ?count) ' . $filter;

        $count_query = urlencode($count_query);
        $count_query = str_replace("+", "%20", $count_query);

        $count_uri = $endpoint . '?query=' . $count_query . '&format=' . urlencode("application/sparql-results+json");

        $response = $this->executeUri($count_uri, $endpoint_user, $endpoint_password);
        $response = json_decode($response);

        // If something goes wrong, the resonse will either be null or false
        if (!$response) {
            \App::abort(500, "Something went wrong while executing the count query. The assembled URI was: $count_uri");
        }

        $count = $response->results->bindings[0]->count->value;

        // Calculate page link headers, previous, next and last based on the count from the previous query
        $paging = Pager::calculatePagingHeaders($limit, $offset, $count);

        $query = $source_definition['query'];
        $query = $this->processParameters($query);

        if (!empty($offset)) {
            $query = $query . " OFFSET $offset ";
        }

        if (!empty($limit)) {
            $query = $query . " LIMIT $limit";
        }

        // Prepare the query with proper encoding for the request
        $query = str_replace('%23', '#', $query);

        $q = urlencode($query);
        $q = str_replace("+", "%20", $q);

        if ($keyword == 'select') {

            $query_uri = $endpoint . '?query=' . $q . '&format=' . urlencode("application/sparql-results+json");

            $response = $this->executeUri($query_uri, $endpoint_user, $endpoint_password);
            $result = json_decode($response);

            if (!$result) {
                \App::abort(500, 'The query has been executed, but the endpoint failed to return sparql results in JSON.');
            }

            $is_semantic = false;

        } else {

            $query_uri = $endpoint . '?query=' . $q . '&format=' . urlencode("application/rdf+xml");

            $response = $this->executeUri($query_uri, $endpoint_user, $endpoint_password);

            // Parse the triple response and retrieve the triples from them
            $result = new \EasyRdf_Graph();
            $parser = new \EasyRdf_Parser_RdfXml();

            $parser->parse($result, $response, 'rdfxml', null);

            $is_semantic = true;
        }

        // Create the data object to return
        $data = new Data();
        $data->data = $result;
        $data->paging = $paging;
        $data->is_semantic = $is_semantic;
        $data->preferred_formats = $this->getPreferredFormats();

        if ($is_semantic) {

            // Fetch the available namespaces and pass
            // them as a configuration of the semantic data result
            $ontologies = $this->ontologies->getAll();

            $prefixes = array();

            foreach ($ontologies as $ontology) {
                $prefixes[$ontology["prefix"]] = $ontology["uri"];
            }

            $data->semantic = new \stdClass();
            $data->semantic->conf = array('ns' => $prefixes);
            $data->preferred_formats = array('ttl', 'jsonld', 'rdf');
        }

        // Determine which parameters were given in the query
        $matches = array();
        $query_parameters = array();

        preg_match_all("/\\$\\{(.+?)\\}/", $source_definition['query'], $matches);

        if (!empty($matches[1])) {

            $matches = $matches[1];


            foreach ($matches as $entry) {
                array_push($query_parameters, $entry);
            }
        }

        $data->optional_parameters = $query_parameters;

        return $data;
    }

    /**
     * Execute a query using cURL and return the result.
     * This function will abort upon error.
     */
    private function executeUri($uri, $user = '', $password = '')
    {
        // Check if curl is installed on this machine
        if (!function_exists('curl_init')) {
            \App::abort(500, "cURL is not installed as an executable on this server, this is necessary to execute the SPARQL query properly.");
        }

        // Initiate the curl statement
        $ch = curl_init();

        // If credentials are given, put the HTTP auth header in the cURL request
        if (!empty($user)) {

            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_USERPWD, $user . ":" . $password);
        }

        // Set the request uri
        curl_setopt($ch, CURLOPT_URL, $uri);

        // Request for a string result instead of having the result being outputted
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);

        if (!$response) {
            $curl_err = curl_error($ch);
            \App::abort(500, "Something went wrong while executing query. The request we put together was: $uri.");
        }

        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // According to the SPARQL 1.1 spec, a SPARQL endpoint can only return 200,400,500 reponses
        if ($response_code == '400') {
            \App::abort(500, "The SPARQL endpoint returned a 400 error. If the SPARQL query contained a parameter, don't forget to pass them as a query string parameter. The error was: $response. The URI was: $uri");
        } elseif ($response_code == '500') {
            \App::abort(500, "The SPARQL endpoint returned a 500 error. If the SPARQL query contained a parameter, don't forget to pass them as a query string parameter. The URI was: $uri");
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Replace parameters in the SPARQL query with the corresponding
     * passed query parameters.
     */
    private function processParameters($query)
    {
        // Fetch the request parameters
        $parameters = \Request::all();

        $placeholders = array();
        $used_parameters = array();

        // Filter out the placeholders in the query
        preg_match_all("/\\$\\{(.+?)\\}/", $query, $placeholders, PREG_SET_ORDER);

        for ($i = 0; $i < count($placeholders); $i++) {

            $placeholder = trim($placeholders[$i][1]);

            // Strip the array notation of the placeholder then keep its placeholders name
            $placeholder_name_pieces = explode('[', $placeholder);
            $placeholder_name = array_shift($placeholder_name_pieces);
            array_push($used_parameters, $placeholder_name);

            $elements = array();

            preg_match_all("/([a-zA-Z]+?)\\.([a-zA-Z]+?)\\('(.*?)','(.*?)'\\)/", $placeholder, $elements, PREG_SET_ORDER);

            if (!empty($elements)) {
                $placeholder = trim($elements[0][1]);
            }

            if (!empty($elements[0]) && count($elements[0]) > 0 && count($elements[0]) != 5) {
                \App::abort(400, "The added placeholder is malformed");
            }

            if (empty($elements)) {

                // ${name[0]}
                $index = strpos($placeholder, "[");

                if ($index !== false) {

                    $placeholder_name = substr($placeholder, 0, $index);
                    $placeholder_index = substr($placeholder, $index + 1, -1);

                    if (!isset($parameters[$placeholder_name])) {
                        $value = '?' . $placeholder;
                    } elseif (!isset($parameters[$placeholder_name][$placeholder_index])) {
                        $value = '?' . $placeholder . '_' . $placeholder_index;
                    } else {
                        $value = $parameters[$placeholder_name][$placeholder_index];
                    }
                } else {

                    if (!isset($parameters[$placeholder])) {
                        $value = '?' . $placeholder;
                    } else {
                        $value = $parameters[$placeholder];
                    }

                    if (is_array($value)) {
                        \App::abort(400, "The parameter $placeholder is single value, however an array as value is given.");
                    }
                }

                $value = addslashes($value);
                $value = urldecode($value);

                $query = str_replace("\${" . $placeholder . "}", $value, $query);

                continue;
            }

            $function = trim($elements[0][2]);
            $pattern = trim($elements[0][3]);
            $concat = trim($elements[0][4]);

            $replacement = $this->processParameterFunction($parameters[$placeholder], $function, $pattern, $concat);

            $placeholder = "\${" . $elements[0][0] . "}";

            $query = str_replace($placeholder, $replacement, $query);

        }

        // Log the non used request parameters
        $parameters = array_except($parameters, $used_parameters);

        // Note that the logging of invalid parameters will happen twice, as we construct and execute
        // the count query as well as the given query
        foreach ($parameters as $key => $value) {

            if (is_array($value)) {
                $log_value = implode(', ', $value);
            } else {
                $log_value = $value;
            }

            \Log::warning("The parameter $key with value $log_value was given as a SPARQL query parameter, but no placeholder in the SPARQL query named $key was found.");
        }

        return $query;
    }

    private function processParameterFunction($values, $function, $pattern, $concat)
    {

        $result = null;

        switch ($function) {
            case "each":

                if (!is_array($values)) {
                    $values = array($values);
                }

                $arr_result = array();

                foreach ($values as $value) {
                    $arr_result[] = str_replace("\$_", "\"$value\"", $pattern);
                }

                $result = implode($concat, $arr_result);

                break;

            default:
                \App::abort(400, "Unknown placeholder function $function");
        }

        return $result;
    }
}
