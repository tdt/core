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

use tdt\core\controllers\spectql\SPECTQLParser;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;
use tdt\core\universalfilter\tablemanager\implementation\tools\TableToPhpObjectConverter;
use tdt\core\universalfilter\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\universalfilter\interpreter\debugging\TreePrinter;

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

        /*
         * DEBUG purposes
         * uncomment to view the Querytree from the spectql query
         */

        $treePrinter = new TreePrinter();
        $tree = $treePrinter->treeToString($universalquery);
        echo "<pre>";
        echo $tree;
        echo "</pre>";
        exit();


        $interpreter = new UniversalInterpreter(new UniversalFilterTableManager());
        $result = $interpreter->interpret($universalquery);

        $converter = new TableToPhpObjectConverter();

        $object = $converter->getPhpObjectForTable($result);

        //pack everything in a new object
        $RESTresource = "spectqlquery";
        $o = new \stdClass();
        $o->$RESTresource = $object;
        $result = $o;

        // Workaround, the spectql tree doesn't accept null as object to start with
        // It gets it header names from it to continue processing the data.
        // Workaround: return object with headernames, but with every datamember = null.
        if($this->isArrayNull($result->spectqlquery)){
            $result->spectqlquery = array();
        }

        $rootname = "spectqlquery";

        // Adjust the paging Link HTTP headers to SPECTQL uri's.
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
                $matches = array();
                if(preg_match('/page=(.*)&page_size=(.*);rel=next.*/',$header,$matches)){

                    $offset = ($matches[1] - 1) * $matches[2];
                    $limit = $matches[2];
                    $next_url = $base_url . ".limit(" . $offset . "," . $limit . "):" . $format;
                    $new_link_header.= $next_url . ";rel=next, ";
                }

                // Previous page link.
                $matches = array();
                if(preg_match('/page=(\d{1,})&page_size=(\d{1,});rel=previous.*/',$header,$matches)){
                    $offset = ($matches[1] - 1) * $matches[2];
                    $limit = $matches[2];
                    $previous_url = $base_url . ".limit(" . $offset . "," . $limit . "):" . $format;
                    $new_link_header.= $previous_url . ";rel=previous, ";
                }

                // Last page link.
                $matches = array();
                if(preg_match('/page=(\d{1,})&page_size=(\d{1,});rel=last.*/',$header,$matches)){
                    $offset = ($matches[1] - 1) * $matches[2];
                    $limit = $matches[2];
                    $last_url = $base_url . ".limit(" . $offset . "," . $limit . "):" . $format;
                    $new_link_header.= $last_url . ";rel=last, ";
                }

                $new_link_header = rtrim($new_link_header,", ");
                header($new_link_header);
            }
        }

        $formatter = new \tdt\formatters\Formatter(strtoupper($format));
        $formatter->execute($rootname,$result);

    }

    /**
     * Check if the object is actually null
     */
    private function isArrayNull($array){
        foreach($array as $arr){
            foreach($arr as $key => $value){
                if(!is_null($value))
                    return false;
            }
        }

        return true;
    }

}