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
/*
include_once("custom/formatters/FormatterFactory.class.php");
include_once("controllers/spectql/SPECTQLParser.class.php");
include_once("model/ResourcesModel.class.php");
include_once("model/DBQueries.class.php");


include_once("universalfilter/interpreter/UniversalInterpreter.php");
include_once("universalfilter/tablemanager/implementation/UniversalFilterTableManager.class.php");
include_once("universalfilter/tablemanager/implementation/tools/TableToPhpObjectConverter.class.php");
 * 
 */
class SPECTQLController extends AController {

    /**
     * This implements the GET
     * 
     */
    function GET($matches) {

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
            $ru = RequestURI::getInstance();
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
                header('WWW-Authenticate: Basic realm="' . Config::get("general","hostname") . Config::get("general","subdir") . '"');
                header('HTTP/1.0 401 Unauthorized');
                exit();
            }
        }



        $parser = new SPECTQLParser($query);
        $context = array(); // array of context variables

        $universalquery = $parser->interpret($context);

        /*
         * DEBUG purposes
         */
        $treePrinter = new TreePrinter();
        $tree = $treePrinter->treeToString($universalquery);
         /*echo "<pre>";
         echo $tree;
         echo "</pre>";*/

        $interpreter = new UniversalInterpreter(new UniversalFilterTableManager());
        $result = $interpreter->interpret($universalquery);

        $converter = new TableToPhpObjectConverter();

        $object = $converter->getPhpObjectForTable($result);

        //pack everything in a new object
        $RESTresource = "spectqlquery";
        $o = new stdClass();
        $o->$RESTresource = $object;
        $result = $o;

        $formatterfactory = FormatterFactory::getInstance($format); //start content negotiation if the formatter factory doesn't exist
        $formatterfactory->setFormat($format);
        $rootname = "spectqlquery";


        $printer = $formatterfactory->getPrinter(strtolower($rootname), $result);
        $printer->printAll();

        $tmpdir = getcwd() . "\\" .  "tmp\\*";
        $files = glob($tmpdir); // get all file names
        foreach ($files as $file) { // iterate files
            
            if (is_file($file))
                unlink($file); // delete file
        }
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
        throw new TDTException(450,array("PUT",$matches["query"]));
    }

    /**
     * You cannot delete a representation
     */
    public function DELETE($matches) {
        throw new TDTException(450,array("DELETE",$matches["query"]));
    }

    /**
     * You cannot use post on a representation
     */
    public function POST($matches) {
        throw new TDTException(450,array("POST",$matches["query"]));
    }

    /**
     * You cannot use patch a representation
     */
    public function PATCH($matches) {
        throw new TDTException(450,array("PATCH",$matches["query"]));
    }   
}

?>
