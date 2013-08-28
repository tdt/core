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
use tdt\core\utility\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SPARQL extends RDFXML {

    protected $max_sorted_top_rows = 10000;

    public function read(&$configObject, $package, $resource) {

        $_GET = $this->php_fix_raw_query();
        $configObject->query = $this->processParameters($configObject->query);    

        $matches = array();
        preg_match_all("/GRAPH\s*?<(.*?)>/", $configObject->query, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $graph = $match[1];
            $replace = \tdt\core\model\DBQueries::getLatestGraph($graph);

            if ($replace)
                $configObject->query = str_replace("GRAPH <$graph>", "GRAPH <$replace>", $configObject->query);
        }

        // Check if there's a limit and/or offset in the query
        // don't apply our paging functinality.
        if(stripos($configObject->query, "limit") || stripos($configObject->query, "offset")){
            $configObject->isPaged = true;
        }

        // Create a count query for paging purposes, this assumes that a where clause is included in the query.
        // Note that the where "clause" is obligatory but it's not mandatory it is preceded by a WHERE keyword.
        $query = $configObject->query;                

        $matches = array();
        $keyword = "";

        if(empty($configObject->isPaged)){
            $prefix = "";

            if (stripos($query, "select") !== FALSE) { // SELECT query
                $keyword = "select";
                $prefix = substr($query, 0, stripos($query,"select"));
            } elseif (stripos($query, "construct") !== FALSE) { // CONSTRUCT query
                $keyword = "construct";
                $prefix = substr($query, 0, stripos($query,"construct"));
            } else { // No valid SPARQL keyword has been found.
                $this->logError("No valid keyword (select or construct) has been found in the query: $query.");

                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(452, array("No valid keyword (select or construct) has been found in the query: $query."), $exception_config);
            }


            $query = preg_replace("/($keyword\s*{.*?})/i", '', $query);               

            if (stripos($query, "where") === FALSE) {
                preg_match('/({[\s\S]+}).*?/i', $query, $matches);
            } else {
                preg_match('/(where\s*{[\s\S]+}).*?/i', $query, $matches);
            }

            if (count($matches) < 2) {
                $message = "Failed to extract the where clause from the sparql query: $query";
                $this->logError($message);

                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(500, array($message), $exception_config);
            }
            
            $query = $matches[0];            

            // Prepare the query to count results.
            $count_query = $prefix . " SELECT (count(*) AS ?count) " . $query;                    

            //Virtuoso doesn't accept url encoded '<' and '>' - signs. So we'll have to replace them by the proper symbol again       
            $count_query = $this->encodeUrl($count_query);

            $configObject->uri = $configObject->endpoint . '?query=' . $count_query . '&format=' . urlencode("application/rdf+xml");
            
            $count_obj = parent::read($configObject, $package, $resource);        
            
            $triples = $count_obj->triples;        

            // Get the results#value, in order to get a count of all the results.
            // This will be used for paging purposes.
            $count = 0;
            foreach ($triples as $triple) {
                if (!empty($triple['p']) && preg_match('/.*sparql-results#value/', $triple['p'])) {
                    $count = $triple['o'];
                }
            }

            $req_uri = "";
            if (!empty($configObject->req_uri)) {
                $req_uri = $configObject->req_uri;
            }

            // Triplestores (e.g. Virtuoso sometimes have a limit to which rows can be sorted)
            // if the given limit is higher than this, adjust the limit and log this re-capping of the limit.
            if($this->limit > $this->max_sorted_top_rows || $this->page_size > $this->max_sorted_top_rows){
                $this->limit = $this->max_sorted_top_rows;
                $this->page_size = $this->max_sorted_top_rows;
                $this->logError("The calculated limit, $this->limit, was too high. We adjusted this to $this->max_sorted_top_rows.");
            }

            // Calculate page link headers, previous and next.
            if ($this->page > 1) {
                $this->setLinkHeader($this->page - 1, $this->page_size, "previous", $req_uri);
            }

            if ($this->limit + $this->offset < $count) {
                $this->setLinkHeader($this->page + 1, $this->page_size, "next", $req_uri);

                $last_page = ceil(round($count / $this->limit, 1));
                if ($last_page > $this->page + 1) {
                    $this->setLinkHeader($last_page, $this->limit, "last", $req_uri);
                }
            }

            $configObject->query = $configObject->query . " OFFSET $this->offset LIMIT $this->limit";
        }
                
        $q = $this->encodeUrl($configObject->query);        
        
        if($keyword == "select"){
            $configObject->uri = $configObject->endpoint . '?query=' . $q . '&format=' . urlencode("application/sparql-results+json");                 
        }else{
            $configObject->uri = $configObject->endpoint . '?query=' . $q . '&format=' . urlencode("application/rdf+xml");
        }

        $configObject->keyword = "select";
        return parent::read($configObject, $package, $resource);
    }

    /**
     * We encode the URL using a sequence of operations, because < and > have to be in the query string
     * while the rest of the string has to be encoded.
     */
    private function encodeUrl($q){
        $q = urlencode($q);
        $q = str_replace('%3C','&lt;',$q);
        $q = str_replace('%3E', '&gt;', $q);        
        $q = html_entity_decode($q);
        $q = str_replace("+", "%20", $q);   
        return $q;
    }

    function php_fix_raw_query() {
        $post = '';

        // Try globals array
        if (!$post && isset($_GLOBALS) && isset($_GLOBALS["HTTP_RAW_POST_DATA"]))
            $post = $_GLOBALS["HTTP_RAW_POST_DATA"];

        // Try globals variable
        if (!$post && isset($HTTP_RAW_POST_DATA))
            $post = $HTTP_RAW_POST_DATA;

        // Try stream
        if (!$post) {
            if (!function_exists('file_get_contents')) {
                $fp = fopen("php://input", "r");
                if ($fp) {
                    $post = '';

                    while (!feof($fp))
                        $post = fread($fp, 1024);

                    fclose($fp);
                }
            } else {
                $post = "" . file_get_contents("php://input");
            }
        }

        $raw = !empty($_SERVER['QUERY_STRING']) ? sprintf('%s&%s', $_SERVER['QUERY_STRING'], $post) : $post;


        $arr = array();
        $pairs = explode('&', $raw);

        foreach ($pairs as $i) {
            if (!empty($i)) {
                list($name, $value) = explode('=', $i, 2);

                if (isset($arr[$name])) {
                    if (is_array($arr[$name])) {
                        $arr[$name][] = $value;
                    } else {
                        $arr[$name] = array($arr[$name], $value);
                    }
                } else {
                    $arr[$name] = $value;
                }
            }
        }

        foreach ($_POST as $key => $value) {
            if (is_array($arr[$key])) {
                $_POST[$key] = $arr[$name];
                $_REQUEST[$key] = $arr[$name];
            }
        }

        foreach ($_GET as $key => $value) {
            if (is_array($arr[$key])) {
                $_GET[$key] = $arr[$name];
                $_REQUEST[$key] = $arr[$name];
            }
        }

        # optionally return result array
        return $arr;
    }

    private function processParameters($query) {
        $param = $_GET;
        //var_dump($param);
        $placeholders = array();        

        preg_match_all("/\\$\\{(.+?)\\}/", $query, $placeholders, PREG_SET_ORDER);
        for ($i = 0; $i < count($placeholders); $i++) {

            $placeholder = trim($placeholders[$i][1]);

            $elements = array();
            //For example ${x.each('?t = $_','||')}
            preg_match_all("/([a-zA-Z]+?)\\.([a-zA-Z]+?)\\('(.*?)','(.*?)'\\)/", $placeholder, $elements, PREG_SET_ORDER);

            if (!empty($elements)){
                $placeholder = trim($elements[0][1]);
            }

            if (!empty($elements[0]) && count($elements[0]) > 0 && count($elements[0]) != 5)
                throw new \tdt\exceptions\TDTException(400, array("The added placeholder is malformed"), array("log_dir" => Config::get("general","logging","path")));

            if (empty($elements)) {
                //${name[0]}
                $index = strpos($placeholder, "[");

                if ($index !== false) {
                    $placeholder_name = substr($placeholder,0, $index);
                    $placeholder_index = substr($placeholder, $index + 1, -1);

                    if (!isset($param[$placeholder_name]))
                        throw new \tdt\exceptions\TDTException(400, array("The parameter $placeholder_name was not provided"), array("log_dir" => Config::get("general","logging","path")));


                    if (!isset($param[$placeholder_name][$placeholder_index]))
                        throw new \tdt\exceptions\TDTException(400, array("The index $placeholder_index of parameter $placeholder does not exist."), array("log_dir" => Config::get("general","logging","path")));

                    $value = $param[$placeholder_name][$placeholder_index];
                } else {

                    if (!isset($param[$placeholder]))
                        throw new \tdt\exceptions\TDTException(400, array("The parameter $placeholder was not provided"), array("log_dir" => Config::get("general","logging","path")));

                    $value = $param[$placeholder];

                    if (is_array($value))
                        throw new \tdt\exceptions\TDTException(400, array("The parameter $placeholder is single value, array given."), array("log_dir" => Config::get("general","logging","path")));
                }
                $value = addslashes($value);

                $query = str_replace("\${" . $placeholder . "}", $value, $query);                
                continue;
            }

            $function = trim($elements[0][2]);
            $pattern = trim($elements[0][3]);
            $concat = trim($elements[0][4]);

            $replacement = $this->processParameterFunction($param[$placeholder], $function, $pattern, $concat);

            $placeholder = "\${" . $elements[0][0] . "}";

            $query = str_replace($placeholder, $replacement, $query);            

        }

        return $query;
    }

    private function processParameterFunction($values, $function, $pattern, $concat) {
        $result = null;

        switch ($function) {
            case "each":
            if (!is_array($values))
                $values = array($values);

            $arr_result = array();
            foreach ($values as $value)
                $arr_result[] = str_replace("\$_", "\"$value\"", $pattern);

            $result = implode($concat, $arr_result);

            break;

            default:
            throw new \tdt\exceptions\TDTException(400, array("Unknown placeholder function $function"), array());
        }

        return $result;
    }

    public function isValid($package_id, $generic_resource_id) {
        // Add limit and offset, we don't need all of the triples, just a few to check if the
        // sparql query is ok.
        $this->uri = $this->endpoint . '?query=' . urlencode($this->query . " offset 0 limit 100") . '&format=' . urlencode("application/rdf+xml");
        return parent::isValid($package_id, $generic_resource_id);
    }

    private function logError($message) {

        $log = new Logger('SPARQL');
        $log->pushHandler(new StreamHandler(Config::get("general", "logging", "path") . "/log_" . date('Y-m-d') . ".txt", Logger::ERROR));
        $log->addError($message);
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
