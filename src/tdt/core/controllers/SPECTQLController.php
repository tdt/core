<?php

/**
 * The controller will handle all SPECTQL requests
 *
 * If it checked all required parameters, checked the format, it will perform the call and get a result. This result is a formatter returned from the FormatterFactory
 *
 * @package The-Datatank/controllers
 * @copyright (C) 2011 by OKFN chapter Belgium vzw/asbl
 * @license LGPL
 * @author Pieter Colpaert
 * @organisation Hogent
 */

namespace tdt\core\controllers;

include_once(__DIR__ . "/../../../../lib/parse_engine.php");
include_once(__DIR__ . "/../controllers/spectql/spectql.php");
include_once(__DIR__ . "/../controllers/SQL/SQLGrammarFunctions.php");

use tdt\core\controllers\spectql\SPECTQLParser;
use tdt\core\formatters\FormatterFactory;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;
use tdt\core\universalfilter\tablemanager\implementation\tools\TableToPhpObjectConverter;
use tdt\core\universalfilter\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\utility\RequestURI;
use tdt\exceptions\TDTException;
use app\core\Config;
use tdt\core\universalfilter\interpreter\debugging\TreePrinter;

class SPECTQLController extends AController {

    public static $TMP_DIR = "";
    
    
    public function __construct() {
        parent::__construct();
        SPECTQLController::$TMP_DIR = __DIR__ . "/../tmp/";
    }

    /**
     * This implements the GET
     *
     */
    public function GET($matches) {

        \tdt\core\utility\Config::setConfig(Config::getConfigArray());
        /*
         * Failsafe for when datablock files don't get deleted
         * by the BigDataBlockManager.
         */

        $tmpdir = SPECTQLController::$TMP_DIR . "*";

        $files = glob($tmpdir);
        foreach ($files as $file) {
            if (filemtime($file) <= time() - 2) {
                unlink($file);
            }
        }

        /*
         * Parse the query
         */
        $query = "/";
        if (isset($matches["query"])) {
            $query = $matches["query"];
        }

        // split off the format of the query, if passed
        $matches = array();
        $format = "";
        if (preg_match("/:[a-zA-Z]+/", $query, $matches)) {
            $format = ltrim($matches[0], ":");
        }

        if ($format == "") {
            //get the current URL
            $ru = RequestURI::getInstance(Config::getConfigArray());
            $pageURL = $ru->getURI();
            $pageURL = rtrim($pageURL, "/");

            //add .about before the ?
            if (sizeof($_GET) > 0) {
                $pageURL = str_replace("?", ":about?", $pageURL);
                $pageURL = str_replace("/:about", ":about", $pageURL);
            } else {
                $pageURL .= ":about";
            }

            header("HTTP/1.1 303 See Other");
            header("Location:" . $pageURL);
        }

        /*
         * We have to make sure the TDTAdmin resources
         * are still hidden from normal users. Using a regex,
         * we're going to find out if the TDTAdmin has been adressed.
         */

        if (preg_match("/.*TDTAdmin.*/i", $query) == 1) {
            if (!$this->isBasicAuthenticated()) {
                //we need to be authenticated
                header('WWW-Authenticate: Basic realm="' . $this->hostname . $this->subdir . '"');
                header('HTTP/1.0 401 Unauthorized');
                exit();
            }
        }

        $parser = new SPECTQLParser($query);
        $context = array(); // array of context variables

        $universalquery = $parser->interpret($context);

        /*
         * DEBUG purposes
         * uncomment to view the Querytree from the spectql query
         */
        /*
        $treePrinter = new TreePrinter();
        $tree = $treePrinter->treeToString($universalquery);
        echo "<pre>";
        echo $tree;
        echo "</pre>";        
        */

        $interpreter = new UniversalInterpreter(new UniversalFilterTableManager());
        $result = $interpreter->interpret($universalquery);

        $converter = new TableToPhpObjectConverter();

        $object = $converter->getPhpObjectForTable($result);

        //pack everything in a new object
        $RESTresource = "spectqlquery";
        $o = new \stdClass();
        $o->$RESTresource = $object;
        $result = $o;

        $formatterfactory = FormatterFactory::getInstance($format); //start content negotiation if the formatter factory doesn't exist
        $formatterfactory->setFormat($format);
        $rootname = "spectqlquery";

        /**
         * adjust header if given from the read() of a resource
         */
        foreach(headers_list() as $header){
            if(substr($header,0,4) == "Link"){
                $ru = RequestURI::getInstance(Config::getConfigArray());
                $pageURL = $ru->getURI();                                
                $new_link_header= "Link:";


                /**
                 * Link to next 
                 * Get the link to next, if present
                 * Adjust the spectql query URL with the next limit(..,..) and format
                 */
                $matches = array();
                if(preg_match('/page=(.*)&page_size=(.*);rel=next.*/',$header,$matches)){
                    $query_matches = array();
                    preg_match('/(.*)\.limit\(.*\)?(:.*)/',$pageURL,$query_matches);
                    $next_query_url = $query_matches[1];
                    $limit = $matches[2];
                    $offset = $limit * ($matches[1] -1 );
                    $next_query_url.= ".limit(" . $offset . "," . $limit . "):" . $format;  
                    $new_link_header.= $next_query_url . ";rel=next;";                  
                }

                /**
                 * Link to previous
                 */
                $matches = array();
                if(preg_match('/page=(\d{1,})&page_size=(\d{1,});rel=previous.*/',$header,$matches)){
                    $query_matches = array();                    
                    preg_match('/(.*)\.limit\(.*\)?(:.*)/',$pageURL,$query_matches);
                    $next_query_url = $query_matches[1];
                    $limit = $matches[2];
                    $offset = $limit * ($matches[1] -1 );
                    $next_query_url.= ".limit(" . $offset . "," . $limit . "):" . $format;  
                    $new_link_header.= $next_query_url . ";rel=previous";                  
                }

                $new_link_header = rtrim($new_link_header,";");                
                /**
                 * Set the adjusted Link header
                 */

                header($new_link_header);               
            }
        }

        $printer = $formatterfactory->getPrinter(strtolower($rootname), $result);
        $printer->printAll();
    }

    function HEAD($matches) {
        $query = "/";
        if (isset($matches["query"])) {
            $query = $matches["query"];
        }
        $parser = new SPECTQLParser($query);
        $context = array(); // array of context variables

        $result = $parser->interpret($context);
        $formatterfactory = FormatterFactory::getInstance("about"); //start content negotiation if the formatter factory doesn't exist
        $rootname = "spectql";


        $printer = $formatterfactory->getPrinter(strtolower($rootname), $result);
        $printer->printHeader();
    }

    /**
     * You cannot PUT on a representation
     */
    function PUT($matches) {
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("PUT", $matches["query"]), $exception_config);
    }

    /**
     * You cannot delete a representation
     */
    public function DELETE($matches) {
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("DELETE", $matches["query"]), $exception_config);
    }

    /**
     * You cannot use post on a representation
     */
    public function POST($matches) {
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("POST", $matches["query"]), $exception_config);
    }

    /**
     * You cannot use patch a representation
     */
    public function PATCH($matches) {
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array("PATCH", $matches["query"]), $exception_config);
    }

}

?>
