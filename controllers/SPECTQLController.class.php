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
include_once("core/lib/parse_engine.php");
include_once("core/controllers/spectql/spectql.php");
include_once("core/controllers/SQL/SQLGrammarFunctions.php");
include_once("core/universalfilter/CombinedFilterGenerators.class.php");

namespace tdt\core\controllers;

use tdt\core\controllers\ACoreController;
use tdt\core\controllers\spectql\SPECTQLParser;
use tdt\core\formatters\FormatterFactory;
use tdt\core\universalfilter\interpreter\UniversalInterpreter;
use tdt\core\universalfilter\tablemanager\implementation\tools\TableToPhpObjectConverter;
use tdt\core\universalfilter\tablemanager\implementation\UniversalFilterTableManager;
use tdt\core\utility\RequestURI;
use tdt\framework\TDTException;

class SPECTQLController extends ACoreController {

    public function __construct() {
        /*
          AutoInclude::register("FormatterFactory", "custom/formatters/FormatterFactory.class.php");
          AutoInclude::register("SPECTQLParser", "cores/core/controllers/spectql/SPECTQLParser.class.php");
          AutoInclude::register("ResourcesModel", "cores/core/model/ResourcesModel.class.php");
          AutoInclude::register("DBQueries", "cores/core/model/DBQueries.class.php");

          AutoInclude::register("UniversalInterpreter", "cores/core/universalfilter/interpreter/UniversalInterpreter.class.php");
          AutoInclude::register("UniversalFilterTableManager", "cores/core/universalfilter/tablemanager/implementation/UniversalFilterTableManager.class.php");
          AutoInclude::register("TableToPhpObjectConverter", "cores/core/universalfilter/tablemanager/implementation/tools/TableToPhpObjectConverter.class.php");

          AutoInclude::register("SPECTQLTokenizer", "cores/core/controllers/spectql/SPECTQLTokenizer.class.php");
          AutoInclude::register("SPECTQLResource", "cores/core/controllers/spectql/SPECTQLResource.class.php");
          AutoInclude::register("SPECTQLTools", "cores/core/controllers/spectql/SPECTQLTools.class.php");
          AutoInclude::register("TreePrinter", "cores/core/universalfilter/interpreter/debugging/TreePrinter.class.php");
          AutoInclude::register("QueryTreeHandler","cores/core/universalfilter/interpreter/other/QueryTreeHandler.class.php"); */
        parent::__construct();
    }

    /**
     * This implements the GET
     *
     */
    public function GET($matches) {

        /*
         * Failsafe for when datablock files don't get deleted
         */

        $tmpdir = "core/tmp/*";

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


        $printer = $formatterfactory->getPrinter(strtolower($rootname), $result);
        $printer->printAll();
    }

    function HEAD($matches) {
        $query = "/";
        if (isset($matches["query"])) {
            $query = $matches["query"];
        }
        $parser = new  SPECTQLParser($query);
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
        throw new TDTException(450, array("PUT", $matches["query"]));
    }

    /**
     * You cannot delete a representation
     */
    public function DELETE($matches) {
        throw new TDTException(450, array("DELETE", $matches["query"]));
    }

    /**
     * You cannot use post on a representation
     */
    public function POST($matches) {
        throw new TDTException(450, array("POST", $matches["query"]));
    }

    /**
     * You cannot use patch a representation
     */
    public function PATCH($matches) {
        throw new TDTException(450, array("PATCH", $matches["query"]));
    }

}

?>
