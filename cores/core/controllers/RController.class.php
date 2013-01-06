<?php

/**
 * This controller will handle the GET request (RController = ReadController)
 * Returning objects of resources, or throwing an exception if something went wrong
 *
 * @package The-Datatank/controllers
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */
class RController extends AController {

    private $formatterfactory;

    public function __construct() {
        AutoInclude::register("RController", "cores/core/controllers/RController.class.php");
        AutoInclude::register("ResourcesModel", "cores/core/model/ResourcesModel.class.php");
        AutoInclude::register("FilterFactory", "cores/core/model/filters/FilterFactory.class.php");
        AutoInclude::register("AFilter", "cores/core/model/filters/AFilter.class.php");
        AutoInclude::register("RESTFilter", "cores/core/model/filters/RESTFilter.class.php");
        AutoInclude::register("SearchFilter", "cores/core/model/filters/SearchFilter.class.php");        
    }

    public function GET($matches) {

        //always required: a package and a resource. 
        $packageresourcestring = $matches["packageresourcestring"];
        $pieces = explode("/", $packageresourcestring);
        $package = array_shift($pieces);

        /**
         * GET operations on TDTAdmin need to be authenticated!
         */
        if ($package == "TDTAdmin") {
            //we need to be authenticated
            if (!$this->isBasicAuthenticated()) {
                header('WWW-Authenticate: Basic realm="' . Config::get("general", "hostname") . Config::get("general", "subdir") . '"');
                header('HTTP/1.0 401 Unauthorized');
                exit();
            }
        }

        $model = ResourcesModel::getInstance();
        $doc = $model->getAllDoc();

        $result = $model->processPackageResourceString($matches["packageresourcestring"]);
        $resourcename = $result["resourcename"];
        $package = $result["packagename"];
        $RESTparameters = $result["RESTparameters"];

        /**
         * Package can also be a part of an entire packagestring if this is the case then a list of links to the other subpackages will have to be listed
         */
        if ($resourcename == "") {
            $packageDoc = $model->getAllPackagesDoc();
            $allPackages = array_keys(get_object_vars($packageDoc));
            $linkObject = new StdClass();
            $links = array();

            /**
             * We only want 1 level deeper, so we're gonna count the amount of /'s in the package
             * and the amount of /'s in the packagestring
             */
            foreach ($allPackages as $packagestring) {
                if (strpos($packagestring, $package) == 0
                        && strpos($packagestring, $package) !== false && $package != $packagestring
                        && substr_count($package, "/") + 1 == substr_count($packagestring, "/")) {
                    $link = Config::get("general", "hostname") . Config::get("general", "subdir") . Config::get("core", "coreprefix") . $packagestring;
                    $packagelinks[] = $link;
                    if (!isset($linkObject->subPackages)) {
                        $linkObject->subPackages = new stdClass();
                    }
                    $linkObject->subPackages = $packagelinks;
                }
            }

            if (isset($doc->$package)) {
                $resourcenames = get_object_vars($doc->$package);
                foreach ($resourcenames as $resourcename => $value) {
                    $link = Config::get("general", "hostname") . Config::get("general", "subdir") . Config::get("core", "coreprefix") . $package . "/" . $resourcename;
                    $links[] = $link;
                    if (!isset($linkObject->resources)) {
                        $linkObject->resources = new stdClass();
                    }
                    $linkObject->resources = $links;
                }
            }

            //This will create an instance of a factory depending on which format is set
            $this->formatterfactory = FormatterFactory::getInstance($matches["format"]);

            $printer = $this->formatterfactory->getPrinter(strtolower($package), $linkObject);
            $printer->printAll();

            exit();
        }

        //This will create an instance of a factory depending on which format is set
        $this->formatterfactory = FormatterFactory::getInstance($matches["format"]);

        $parameters = $_GET;
        $requiredParameters = array();

        foreach ($doc->$package->$resourcename->requiredparameters as $parameter) {
            //set the parameter of the method

            if (!isset($RESTparameters[0])) {
                throw new TDTException(452, array("Invalid parameter given: $parameter"));
            }
            $parameters[$parameter] = $RESTparameters[0];
            //removes the first element and reindex the array - this way we'll only keep the object specifiers (RESTful filtering) in this array
            array_shift($RESTparameters);
        }


        $result = $model->readResource($package, $resourcename, $parameters, $RESTparameters);

        //maybe the resource reinitialised the database, so let's set it up again with our config, just to be sure.
        R::setup(Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"), Config::get("db", "user"), Config::get("db", "password"));

        // apply RESTFilter
        $subresources = array();
        $filterfactory = FilterFactory::getInstance();

        if (sizeof($RESTparameters) > 0) {
            if (!(is_subclass_of($result, 'Model') || is_a($result, 'Model'))) {
                $RESTFilter = $filterfactory->getFilter("RESTFilter", $RESTparameters);
                $resultset = $RESTFilter->filter($result);
                $subresources = $resultset->subresources;
                $result = $resultset->result;
            }
        }
        // Apply Lookup filter if asked, this has been implemented according to the 
        // Open Search Specifications

        if (isset($_GET["filterBy"]) && isset($_GET["filterValue"])) {
            if (is_array($result)) {
                $filterparameters = array();
                $filterparameters["filterBy"] = $_GET["filterBy"];
                $filterparameters["filterValue"] = $_GET["filterValue"];
                if (isset($_GET["filterOp"])) {
                    $filterparameters["filterOp"] = $_GET["filterOp"];
                }

                $searchFilter = $filterfactory->getFilter("SearchFilter", $filterparameters);
                $result = $searchFilter->filter($result);
            }
        }

        //pack everything in a new object
        $o = new stdClass();
        $RESTresource = "";
        if (sizeof($RESTparameters) > 0) {
            $RESTresource = $RESTparameters[sizeof($RESTparameters) - 1];
        } else {
            $RESTresource = $resourcename;
        }

        $o->$RESTresource = $result;
        $result = $o;

        // get the according formatter from the factory
        $printer = $this->formatterfactory->getPrinter($resourcename, $result);
        $printer->printAll();
    }

    private function getAllSubPackages($package, &$linkObject, &$links) {
        $model = ResourcesModel::getInstance();
        $packageDoc = $model->getAllPackagesDoc();
        $allPackages = array_keys(get_object_vars($packageDoc));

        foreach ($allPackages as $packagestring) {
            if (strpos($packagestring, $package) == 0
                    && strpos($packagestring, $package) !== false && $package != $packagestring) {

                $foundPackage = TRUE;
                $link = Config::get("general", "hostname") . Config::get("general", "subdir") . $packagestring;
                $links[] = $link;
                if (!isset($linkObject->subPackages)) {
                    $linkObject->subPackages = new stdClass();
                }
                $linkObject->subPackages->$package = $links;
            }
        }
    }

    public function HEAD($matches) {

        //always required: a package and a resource. 
        $packageresourcestring = $matches["packageresourcestring"];
        $pieces = explode("/", $packageresourcestring);
        $package = array_shift($pieces);

        /**
         * Even GET operations on TDTAdmin need to be authenticated!
         */
        if ($package == "TDTAdmin") {
            //we need to be authenticated
            if (!$this->isBasicAuthenticated()) {
                header('WWW-Authenticate: Basic realm="' . Config::get("general", "hostname") . Config::get("general", "subdir") . '"');
                header('HTTP/1.0 401 Unauthorized');
                exit();
            }
        }

        //Get an instance of our resourcesmodel
        $model = ResourcesModel::getInstance();
        $doc = $model->getAllDoc();

        /**
         * Since we do not know where the package/resource/requiredparameters end, we're going to build the package string
         * and check if it exists, if so we have our packagestring. Why is this always correct ? Take a look at the 
         * ResourcesModel class -> funcion isResourceValid()
         */
        $foundPackage = FALSE;
        $resourcename = "";
        $reqparamsstring = "";

        if (!isset($doc->$package)) {
            while (!empty($pieces)) {
                $package .= "/" . array_shift($pieces);
                if (isset($doc->$package)) {
                    $foundPackage = TRUE;
                    $resourcename = array_shift($pieces);
                    $reqparamsstring = implode("/", $pieces);
                }
            }
        } else {
            $foundPackage = TRUE;
            $resourceNotFound = TRUE;
            while (!empty($pieces) && $resourceNotFound) {
                $resourcename = array_shift($pieces);
                if (!isset($doc->$package->$resourcename) && $resourcename != NULL) {
                    $package .= "/" . $resourcename;
                    $resourcename = "";
                } else {
                    $resourceNotFound = FALSE;
                }
            }
            $reqparamsstring = implode("/", $pieces);
        }

        $RESTparameters = array();
        $RESTparameters = explode("/", $reqparamsstring);
        if ($RESTparameters[0] == "") {
            $RESTparameters = array();
        }

        /**
         * Package can also be a part of an entire packagestring if this is the case then a list of links to the other subpackages will have to be listed
         */
        if ($foundPackage && $resourcename == "") {
            $packageDoc = $model->getAllPackagesDoc();
            $allPackages = array_keys(get_object_vars($packageDoc));
            $linkObject = new StdClass();
            $links = array();

            /**
             * We only want 1 level deeper, so we're gonna count the amount of /'s in the package
             * and the amount of /'s in the packagestring
             */
            foreach ($allPackages as $packagestring) {
                if (strpos($packagestring, $package) == 0
                        && strpos($packagestring, $package) !== false && $package != $packagestring
                        && substr_count($package, "/") + 1 == substr_count($packagestring, "/")) {

                    $foundPackage = TRUE;
                    $link = Config::get("general", "hostname") . Config::get("general", "subdir") . Config::get("core", "coreprefix") . $packagestring;
                    $packagelinks[] = $link;
                    if (!isset($linkObject->subPackages)) {
                        $linkObject->subPackages = new stdClass();
                    }
                    $linkObject->subPackages->$package = $packagelinks;
                }
            }

            if (isset($doc->$package)) {
                $foundPackage = TRUE;
                $resourcenames = get_object_vars($doc->$package);
                foreach ($resourcenames as $resourcename => $value) {
                    $link = Config::get("general", "hostname") . Config::get("general", "subdir") . Config::get("core", "coreprefix") . $package . "/" . $resourcename;
                    $links[] = $link;
                    if (!isset($linkObject->resources)) {
                        $linkObject->resources = new stdClass();
                    }
                    $linkObject->resources->$package = $links;
                }
            } else {
                if (!$foundPackage) {
                    throw new TDTException(404, array($packageresourcestring));
                }
            }

            //This will create an instance of a factory depending on which format is set
            $this->formatterfactory = FormatterFactory::getInstance($matches["format"]);

            $printer = $this->formatterfactory->getPrinter(strtolower($package), $linkObject);
            $printer->printHeader();
            exit();
        }


        if (!$foundPackage) {
            throw new TDTException(404, array($packageresourcestring));
        }

        /**
         * At this stage a package and a resource have been passed, lets check if they exists, and if so lets call the read()
         * action and return the result.
         */
        //This will create an instance of a factory depending on which format is set
        $this->formatterfactory = FormatterFactory::getInstance($matches["format"]);

        if (!isset($doc->$package) || !isset($doc->$package->$resourcename)) {
            throw new TDTException(404, array($packageresourcestring));
        }

        $parameters = $_GET;

        foreach ($doc->$package->$resourcename->requiredparameters as $parameter) {
            //set the parameter of the method
            if (!isset($RESTparameters[0])) {
                throw new TDTException(452, array("Invalid parameter:" . $parameter));
            }
            $parameters[$parameter] = $RESTparameters[0];
            //removes the first element and reindex the array - this way we'll only keep the object specifiers (RESTful filtering) in this array
            array_shift($RESTparameters);
        }

        $result = $model->readResource($package, $resourcename, $parameters, $RESTparameters);

        //maybe the resource reinitialised the database, so let's set it up again with our config, just to be sure.
        R::setup(Config::get("db", "system") . ":host=" . Config::get("db", "host") . ";dbname=" . Config::get("db", "name"), Config::get("db", "user"), Config::get("db", "password"));

        /**
         * Apply filters to the resulting object
         * TODO : put the OSpec filter into the .lime file as a grammar, and let the AST handle the filtering
         * 1) RESTfilter
         * 2) OSpec filter
         */
        // apply RESTFilter
        $subresources = array();
        $filterfactory = FilterFactory::getInstance();

        if (sizeof($RESTparameters) > 0) {
            if (!(is_subclass_of($result, 'Model') || is_a($result, 'Model'))) {
                $RESTFilter = $filterfactory->getFilter("RESTFilter", $RESTparameters);
                $resultset = $RESTFilter->filter($result);
                $subresources = $resultset->subresources;
                $result = $resultset->result;
            }
        }
        // Apply Lookup filter if asked, this has been implemented according to the 
        // Open Search Specifications

        if (isset($_GET["filterBy"]) && isset($_GET["filterValue"])) {
            if (is_array($result)) {
                $filterparameters = array();
                $filterparameters["filterBy"] = $_GET["filterBy"];
                $filterparameters["filterValue"] = $_GET["filterValue"];
                if (isset($_GET["filterOp"])) {
                    $filterparameters["filterOp"] = $_GET["filterOp"];
                }
                $searchFilter = $filterfactory->getFilter("SearchFilter", $filterparameters);
                $result = $searchFilter->filter($result);
            }
        }

        //pack everything in a new object
        $o = new stdClass();
        $RESTresource = "";
        if (sizeof($RESTparameters) > 0) {
            $RESTresource = $RESTparameters[sizeof($RESTparameters) - 1];
        } else {
            $RESTresource = $resourcename;
        }

        $o->$RESTresource = $result;
        $result = $o;

        // get the according formatter from the factory
        $printer = $this->formatterfactory->getPrinter(strtolower($resourcename), $result);
        $printer->printHeader();
    }

    /**
     * You cannot PUT on a representation
     */
    function PUT($matches) {
        throw new TDTException(450, array("PUT", $matches["packageresourcestring"]));
    }

    /**
     * You cannot delete a representation
     */
    public function DELETE($matches) {
        throw new TDTException(450, array("DELETE", $matches["packageresourcestring"]));
    }

    /**
     * You cannot use post on a representation
     */
    public function POST($matches) {
        throw new TDTException(450, array("POST", $matches["packageresourcestring"]));
    }

    /**
     * You cannot use patch a representation
     */
    public function PATCH($matches) {
        throw new TDTException(450, array("PATCH", $matches["packageresourcestring"]));
    }

    // visualizations may not be logged
    private function isVisualization($format) {
        $vis = array("map", "grid", "bar", "chart", "column", "pie");
        return in_array($format, $vis);
    }

}

?>
