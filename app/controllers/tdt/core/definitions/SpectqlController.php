<?php

/**
 * SpectqlController: Controller that handles SPECTQL queries.
 *
 * @copyright (C) 2011 by OKFN chapter Belgium vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 * @author Michiel Vancoillie
 */

namespace tdt\core\definitions;

include_once(__DIR__ . "/../spectql/parse_engine.php");
include_once(__DIR__ . "/../spectql/source/spectql.php");
include_once(__DIR__ . "/../spectql/implementation/SqlGrammarFunctions.php");

use tdt\core\spectql\source\SPECTQLParser;
use tdt\core\spectql\implementation\interpreter\UniversalInterpreter;
use tdt\core\spectql\implementation\tablemanager\implementation\tools\TableToPhpObjectConverter;
use tdt\core\spectql\implementation\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\spectql\implementation\interpreter\debugging\TreePrinter;
use tdt\core\ContentNegotiator;
use tdt\core\datasets\Data;

class SPECTQLController extends \Controller {

    public static $TMP_DIR = "";

    // TODO review
    public function __construct() {
        parent::__construct();
        SPECTQLController::$TMP_DIR = __DIR__ . "/../tmp/";
    }

    /**
     * Apply the given SPECTQL query to the data and return the result.
     */
    public static function handle($uri) {

        // Propagate the request based on the HTTPMethod of the request
        $method = \Request::getMethod();

        switch($method){
            case "GET":

                $uri = ltrim($uri, '/');
                return self::performQuery($uri);
                break;
            default:
                // Method not supported
                \App::abort(405, "The HTTP method '$method' is not supported by this resource.");
                break;
        }
    }

    /**
     * Perform the SPECTQL query.
     */
    private static function performQuery($uri){

        // Failsafe, for when datablocks don't get deleted by the BigDataBlockManager.
        // TODO remove this failsafe, cut the bigdatablockmanager loose from the functionality
        // If a file is too big to handle, return a 500 error.
        $tmpdir = SPECTQLController::$TMP_DIR . "*";

        $query = "/";

        // Split off the format of the SPECTQL query
        $format = "";

        if (preg_match("/:[a-zA-Z]+/", $query, $matches)) {
            $format = ltrim($matches[0], ":");
        }

        if ($format == "") {

            // Get the current URL
            $pageURL = \Request::path();
            $pageURL = rtrim($pageURL, "/");
        }

        $parser = new SPECTQLParser($uri);

        $context = array(); // array of context variables

        $universalquery = $parser->interpret($context);


        // Display the query tree, uncomment in case of debugging
        /*
        $treePrinter = new TreePrinter();
        $tree = $treePrinter->treeToString($universalquery);
        echo "<pre>";
        echo $tree;
        echo "</pre>";
        exit();*/

        $interpreter = new UniversalInterpreter(new UniversalFilterTableManager());
        $result = $interpreter->interpret($universalquery);

        $converter = new TableToPhpObjectConverter();

        $object = $converter->getPhpObjectForTable($result);

        $rootname = "spectqlquery";

        // Adjust the paging Link HTTP headers to SPECTQL uri's.
        //TODO
        /*
        foreach(headers_list() as $header){
            if(substr($header,0,4) == "Link"){
                $ru = RequestURI::getInstance(Config::getConfigArray());
                $pageURL = $ru->getURI();
                $new_link_header= "Link:";

                // cut off the format, position = position of the ':' before the format
                $position = strrpos($pageURL,":");
                $base_url = substr($pageURL,0,$position);

                // Cut off the limit() clause if present.
                $base_url = preg_replace('/(\.limit\(.*\))/','',$base_url);


                // Next page link.
                $matches = array();$*/
                //if(preg_match('/page=(.*)&page_size=(.*);rel=next.*/',$header,$matches)){
/*
                    $offset = ($matches[1] - 1) * $matches[2];
                    $limit = $matches[2];
                    $next_url = $base_url . ".limit(" . $offset . "," . $limit . "):" . $format;
                    $new_link_header.= $next_url . ";rel=next, ";
                }

                // Previous page link.
                $matches = array();*/
                //if(preg_match('/page=(\d{1,})&page_size=(\d{1,});rel=previous.*/',$header,$matches)){
                  /*  $offset = ($matches[1] - 1) * $matches[2];
                    $limit = $matches[2];
                    $previous_url = $base_url . ".limit(" . $offset . "," . $limit . "):" . $format;
                    $new_link_header.= $previous_url . ";rel=previous, ";
                }

                // Last page link.
                $matches = array();*/
                //if(preg_match('/page=(\d{1,})&page_size=(\d{1,});rel=last.*/',$header,$matches)){
                  /*  $offset = ($matches[1] - 1) * $matches[2];
                    $limit = $matches[2];
                    $last_url = $base_url . ".limit(" . $offset . "," . $limit . "):" . $format;
                    $new_link_header.= $last_url . ";rel=last, ";
                }

                $new_link_header = rtrim($new_link_header,", ");
                header($new_link_header);
            }
        }*/
        // Return the formatted response with content negotiation

        // Get REST parameters
        $definition_uri = preg_match('/(.*?)\{.*/', $uri, $matches);
        $definition_uri = $matches[1];
        $definition = DefinitionController::get($definition_uri);

        if(!empty($definition)){
            $source_definition = $definition->source()->first();
        }

        $rest_parameters = str_replace($definition->collection_uri . '/' . $definition->resource_name, '', $uri);
        $rest_parameters = ltrim($rest_parameters, '/');
        $rest_parameters = explode('/', $rest_parameters);

        if(empty($rest_parameters[0]) && !is_numeric($rest_parameters[0])){
            $rest_parameters = array();
        }
        $data = new Data();
        $data->data = $object;
        $data->rest_parameters = $rest_parameters;

        // Add definition to the object
        $data->definition = $definition;

        // Add source definition to the object
        $data->source_definition = $source_definition;

        // Return the formatted response with content negotiation
        return ContentNegotiator::getResponse($data, 'json');

    }

    /**
     * Check if the object is actually null
     */
    private static function isArrayNull($array){
        foreach($array as $arr){
            foreach($arr as $key => $value){
                if(!is_null($value))
                    return false;
            }
        }

        return true;
    }

}