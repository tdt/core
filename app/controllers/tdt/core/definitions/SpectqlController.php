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

    // TODO make sure we don't need a tmp dir to store any spectql related stuff anymore
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

        // Fetch the original uri, which is a hassle since our spectql format allows for a ? - character
        // identify the start of a filter, the Request class sees this is the start of query string parameters
        // and fails to parse them as they only contain keys, but never values ( our spectql filter syntax is nowhere near
        // the same as a query string parameter sequence). Therefore, we need to build our spectql uri manually.
        // Furthermore, after the ? - character dots are replaced with underscores by PHP itself. http://ca.php.net/variables.external
        // This is another reason why we build the query string to be passed to the parser ourselves.

        // The Request class also seems to have an issue with evaluating a semi-colon in the query string
        // It puts the semi-colon and what follows next to the first query string parameter, IF there are multiple
        // query string parameters (lon>5&lon<10), since this isn't really supported by PHP, Request from Symfony tries
        // apparently a best effort at fixing this.

        $filter = "";

        $original_uri = \Request::fullUrl();
        $root = \Request::root();

        if(preg_match("%$root\/spectql\/(.*)%", $original_uri, $matches)){
            $query_uri = urldecode($matches[1]);
        }

        $format = "";

        // Fetch the format of the query
        if(preg_match("/.*(:[a-zA-Z]+)&?(.*?)/", $query_uri, $matches)){
            $format = ltrim($matches[1], ":");
        }

        // Remove the format and any following query string parameters
        if(!empty($format)){
            $query_uri = preg_replace("/:" . $format . "\??.*/", '', $query_uri);
        }

        // Initialize the parser with our query string
        $parser = new SPECTQLParser($query_uri);

        $context = array(); // array of context variables

        $universalquery = $parser->interpret($context);

        // Display the query tree, uncomment in case of debugging
        $treePrinter = new TreePrinter();
        /*$tree = $treePrinter->treeToString($universalquery);
        echo "<pre>";
        echo $tree;
        echo "</pre>";*/

        $interpreter = new UniversalInterpreter(new UniversalFilterTableManager());
        $result = $interpreter->interpret($universalquery);

        // Convert the resulting table object to a php object
        $converter = new TableToPhpObjectConverter();
        $object = $converter->getPhpObjectForTable($result);

        // Perform a clean-up, every property that is empty can be thrown away
        foreach($object as $index => $property){
            if(self::isArrayNull($property)){
                unset($object[$index]);
            }
        }

        $rootname = "spectqlquery";

        // Get the required properties for the Data object
        $definition_uri = preg_match('/(.*?)\{.*/', $uri, $matches);

        // If no selection statement is given, abort the processing of the query
        if(empty($matches)){
            \App::abort(400, "Please provide a select statement with the SPECTQL query (e.g. { column_1, column_2 }).");
        }

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

        // Specify it's a SPECTQL result
        $data->is_spectql = true;

        $data->rest_parameters = $rest_parameters;

        // Add definition to the object
        $data->definition = $definition;

        // Add source definition to the object
        $data->source_definition = $source_definition;

        // Return the formatted response with content negotiation
        return ContentNegotiator::getResponse($data, $format);
    }

    /**
     * Check if the property contains data
     *
     *
     */
    private static function isArrayNull($array){

        foreach($array as $entry){
            if(!empty($entry)){
                return false;
            }
        }

        return true;
    }

}