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
        $this->php_fix_raw_query();
        $configObject->query = $this->processParameters($configObject->query);

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
                 
                if (isset($arr[$name]) ) {
                    if (is_array($arr[$name]) ) {
                        $arr[$name][] = $value;
                    } else {
                        $arr[$name] = array($arr[$name], $value);
                    }
                } else {
                    $arr[$name] = $value;
                }
            }
        }
         
        foreach ( $_POST as $key => $value ) {
            if (is_array($arr[$key]) ) {
                $_POST[$key] = $arr[$name];
                $_REQUEST[$key] = $arr[$name];
            }
        }
                 
        foreach ( $_GET as $key => $value ) {
            if (is_array($arr[$key]) ) {
                $_GET[$key] = $arr[$name];
                $_REQUEST[$key] = $arr[$name];
            }
        }

    # optionally return result array 
        return $arr;
    }

    private function processParameters($query) {
        $param = get_object_vars($this);
        unset($param["rest_params"]);

        $placeholders = array();
        preg_match_all("/\\$\\{(.+?)\\}/", $query, $placeholders);

        for ($i = 1; $i < count($placeholders); $i++) {
            $placeholder = trim($placeholders[$i][0]);

            $elements = array();
            //For example ${x.each('?t = $_','||')}
            preg_match_all("/([a-zA-Z]+?)\\.([a-zA-Z]+?)\\('(.*?)','(.*?)'\\)/", $placeholder, $elements, PREG_SET_ORDER);

            if (!empty($elements))
                $placeholder = trim($elements[0][1]);

            if (count($elements[0]) > 0 && count($elements[0]) != 5)
                throw new \tdt\exceptions\TDTException(400, array("The added placeholder is malformed"), array());

            if (!isset($param[$placeholder]))
                throw new \tdt\exceptions\TDTException(400, array("The parameter $placeholder was not provided"), array());

            if (empty($elements)) {
                $value = $param[$placeholder];
                if (!is_array($value))
                    throw new \tdt\exceptions\TDTException(400, array("The parameter $placeholder is single value, array given."), array());
                
                $value = addslashes($value);
                $query = str_replace("\${" . $placeholder . "}", "\"$value\"", $query);
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