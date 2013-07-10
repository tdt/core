<?php

/**
 * This is the controller which will handle Real-World objects. So CUD actions will be handled.
 *
 * @package The-Datatank/controllers
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Pieter Colpaert
 * @author Jan Vansteenlandt
 */

namespace tdt\core\controllers;

use tdt\core\controllers\RController;
use tdt\core\model\ResourcesModel;
use tdt\exceptions\TDTException;
use app\core\Config;

class CUDController extends AController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * You cannot get a real-world object, only its representation. Therefore we're going to redirect you to .about which will do content negotiation.
     */
    public function GET($matches) {

        $packageresourcestring = $matches[0];
        $packageresourcestring = strtolower($packageresourcestring);

        
        // Get the format of the string.
        $dotposition = strrpos($packageresourcestring, ".");
        $format = substr($packageresourcestring, $dotposition);
        $format = ltrim($format, ".");
        $end = $dotposition;

        $packageresourcestring = substr($packageresourcestring, 0, $end);

        $matches["packageresourcestring"] = ltrim($packageresourcestring, "/");
        $matches["format"] = $format;
        $RController = new RController();
        $RController->GET($matches);
    }

    public function HEAD($matches) {


        $packageresourcestring = $matches[0];

        // Get the format of the string.
        $dotposition = strrpos($packageresourcestring, ".");
        $format = substr($packageresourcestring, $dotposition);
        $format = ltrim($format, ".");
        $end = $dotposition - 1;
        $packageresourcestring = substr($packageresourcestring, 1, $end);

        // Fill in the matches array to redirect to the HEAD function.
        $matches["packageresourcestring"] = ltrim($packageresourcestring, "/");
        $matches["format"] = $format;
        $RController = new RController();
        $RController->HEAD($matches);
    }

    function PUT($matches) {
            
        $packageresourcestring = $matches["packageresourcestring"];
        $packageresourcestring = strtolower($packageresourcestring);
        $packageresourcestring = rtrim($packageresourcestring,"/");
        $pieces = explode("/", $packageresourcestring);

        // Check for empty pieces.
        foreach($pieces as $piece){
            if($piece == ""){
                $exception_config = array();
                $exception_config["log_dir"] = Config::get("general", "logging", "path");
                $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
                throw new TDTException(452, array("We found an empty piece in our package-resourcestring, passing a double / might be a the origin of this error. Passed package-resourcestring: $packageresourcestring"), $exception_config, $exception_config);
            }
        }

        //both package and resource set?
        if (count($pieces) < 2) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(452, array($packageresourcestring), $exception_config, $exception_config);
        }

        //fetch all the PUT variables in one array
        $HTTPheaders = getallheaders();
        if (isset($HTTPheaders["Content-Type"]) && $HTTPheaders["Content-Type"] == "application/json") {
            $json_string = file_get_contents("php://input");
            $params = json_decode($json_string,true);

            // Check if the object is wrapped or not (e.g. are the parameters already in the object, or are these wrapped.)
            $param_object = array_shift($params);
            if(is_array($param_object)){
                $params = $param_object;
            }else{
                $params = json_decode($json_string,true);
            }
        } else {
            parse_str(file_get_contents("php://input"), $params);
        }

        $model = ResourcesModel::getInstance(Config::getConfigArray());
        $RESTparameters = array();

        $model->createResource($packageresourcestring, $params);
        header("Content-Location: " . $this->hostname . $this->subdir . $packageresourcestring);

        //maybe the resource reinitialised the database, so let's set it up again with our config, just to be sure.
        $this->initializeDatabaseConnection();

        //Clear the documentation in our cache for it has changed
        $this->clearCachedDocumentation();
    }

    /**
     * Delete a resource (There is some room for improvement of queries, or division in subfunctions but for now,
     * this'll do the trick)
     * @param string $matches The matches from the given URL, contains the package and the resource from the URL
     */
    public function DELETE($matches) {

        $model = ResourcesModel::getInstance(Config::getConfigArray());
        $doc = $model->getAllDoc();

        //always required: a package and a resource.
        $packageresourcestring = $matches["packageresourcestring"];
        $packageresourcestring = strtolower($packageresourcestring);

        $pieces = explode("/", $packageresourcestring);
        $package = array_shift($pieces);

        $RESTparameters = array();

        /**
         * Since we do not know where the package/resource/requiredparameters end, we're going to build the package string
         * and check if it exists, if so we have our packagestring. Why is this always correct ? Take a look at the
         * ResourcesModel class -> funcion isResourceValid()
         */
        $foundPackage = FALSE;
        $resource = "";
        $reqparamsstring = "";

        if (!isset($doc->$package)) {
            while (!empty($pieces)) {
                $package .= "/" . array_shift($pieces);
                if (isset($doc->$package)) {
                    $foundPackage = TRUE;
                    $resource = array_shift($pieces);
                    $reqparamsstring = implode("/", $pieces);
                }
            }
        } else {
            $foundPackage = TRUE;
            $resource = array_shift($pieces);
            $reqparamsstring = implode("/", $pieces);
        }

        $RESTparameters = array();
        $RESTparameters = explode("/", $reqparamsstring);
        if ($RESTparameters[0] == "") {
            $RESTparameters = array();
        }

        $packageDoc = $model->getAllPackagesDoc();
        if (!$foundPackage && !isset($packageDoc->$package)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($packageresourcestring), $exception_config);
        }

        //delete the package and resource when authenticated and authorized in the model
        $model = ResourcesModel::getInstance(Config::getConfigArray());
        if ($resource == "") {
            $model->deletePackage($package);
        } else {
            $model->deleteResource($package, $resource, $RESTparameters);
        }

        // make sure we connect to the correct database.
        $this->initializeDatabaseConnection();

        //Clear the documentation in our cache for it has changed
        $this->clearCachedDocumentation();
    }

    /**
     * PATCH is a relatively new request HTTP HEADER which will be used to update a piece of a resource definition.
     */
    public function PATCH($matches) {

        $model = ResourcesModel::getInstance(Config::getConfigArray());
        $doc = $model->getAllDoc();

        //always required: a package and a resource.
        $packageresourcestring = $matches["packageresourcestring"];
        $packageresourcestring = strtolower($packageresourcestring);
        $pieces = explode("/", $packageresourcestring);
        $package = array_shift($pieces);

        $RESTparameters = array();

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

        if (!$foundPackage) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($packageresourcestring), $exception_config);
        }

        //both package and resource set?
        if ($resourcename == "") {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($packageresourcestring), $exception_config);
        }

        // patch (array) contains all the patch parameters
        $patch = array();
        $HTTPheaders = getallheaders();
        if (isset($HTTPheaders["Content-Type"]) && $HTTPheaders["Content-Type"] == "application/json") {
            $json_string = file_get_contents("php://input");
            $patch = json_decode($json_string,true);

            // Check if the object is wrapped or not (e.g. are the parameters already in the object, or are these wrapped.)
            $param_object = array_shift($patch);
            if(is_array($param_object)){
                $patch = $param_object;
            }else{
                $patch = json_decode($json_string,true);
            }
        } else {
            parse_str(file_get_contents("php://input"), $patch);
        }

        $model = ResourcesModel::getInstance(Config::getConfigArray());
        $model->updateResource($package, $resourcename, $patch, $RESTparameters);

        //maybe the resource reinitialised the database, so let's set it up again with our config, just to be sure.
        $this->initializeDatabaseConnection();
        //Clear the documentation in our cache for it has changed
        $this->clearCachedDocumentation();
    }

    /**
     * POST is currently not used
     */
    public function POST($matches) {
        $exception_config = array();
        $exception_config["log_dir"] = Config::get("general", "logging", "path");
        $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
        throw new TDTException(450, array(), $exception_config);
    }

}