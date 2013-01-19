<?php

/**
 * This class will handle a remote resource and connect to another DataTank instance for their data
 *
 * @package The-Datatank/model
 * @copyright (C) 2011 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt
 * @author Pieter Colpaert
 */

namespace tdt\core\model;

use tdt\core\model\DBQueries;
use tdt\core\model\resources\create\RemoteResourceCreator;
use tdt\core\model\resources\delete\RemoteResourceDeleter;
use tdt\core\model\resources\read\RemoteResourceReader;
use tdt\core\utility\Config;
use tdt\framework\Request;
use tdt\exceptions\TDTException;

class RemoteResourceFactory extends AResourceFactory {

    public function __construct() {
        /* AutoInclude::register("RemoteResourceCreator","cores/core/model/resources/create/RemoteResourceCreator.class.php");
          AutoInclude::register("RemoteResourceReader","cores/core/model/resources/read/RemoteResourceReader.class.php");
          AutoInclude::register("RemoteResourceDeleter","cores/core/model/resources/delete/RemoteResourceDeleter.class.php");
          AutoInclude::register("Request","cores/framework/Request.class.php"); */
    }

    public function hasResource($package, $resource) {
        $rn = $this->getAllResourceNames();
        return isset($rn[$package]) && in_array($resource, $rn[$package]);
    }

    protected function getAllResourceNames() {
        $resultset = DBQueries::getAllRemoteResourceNames();
        $resources = array();
        foreach ($resultset as $result) {
            if (!isset($resources[$result["package_name"]])) {
                $resources[$result["package_name"]] = array();
            }
            $resources[$result["package_name"]][] = $result["res_name"];
        }
        return $resources;
    }

    public function createCreator($package, $resource, $parameters, $RESTparameters) {
        $creator = new RemoteResourceCreator($package, $resource, $RESTparameters);
        foreach ($parameters as $key => $value) {
            $creator->setParameter($key, $value);
        }
        return $creator;
    }

    public function createReader($package, $resource, $parameters, $RESTparameters) {
        $reader = new RemoteResourceReader($package, $resource, $RESTparameters, $this->fetchResourceDocumentation($package, $resource));
        $reader->processParameters($parameters);
        return $reader;
    }

    public function createDeleter($package, $resource, $RESTparameters) {
        return new RemoteResourceDeleter($package, $resource, $RESTparameters);
    }

    public function makeDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }
            foreach ($resourcenames as $resource) {
                $doc->$package->$resource = new \stdClass();
                $doc->$package->$resource = $this->fetchResourceDocumentation($package, $resource);
            }
        }
    }

    public function makeDescriptionDoc($doc) {
        foreach ($this->getAllResourceNames() as $package => $resourcenames) {
            if (!isset($doc->$package)) {
                $doc->$package = new \stdClass();
            }
            foreach ($resourcenames as $resource) {
                $doc->$package->$resource = new \stdClass();
                /**
                 * Get the metadata properties
                 */
                $metadata = DBQueries::getMetaData($package, $resource);
                if (!empty($metadata)) {
                    foreach ($metadata as $name => $value) {
                        if ($name != "id" && $name != "resource_id") {
                            $doc->$package->$resource->$name = $value;
                        }
                    }
                }
                $doc->$package->$resource = $this->fetchResourceDescription($package, $resource);
            }
        }
    }

    public function makeDeleteDoc($doc) {
        $d = new \stdClass();
        $d->documentation = "You can delete every remote resource by sending a DELETE HTTP request to the resource definition located in TDTAdmin/Resources.";
        if (!isset($doc->delete)) {
            $doc->delete = new \stdClass();
        }
        $doc->delete->remote = new \stdClass();
        $doc->delete->remote = $d;
    }

    public function makeCreateDoc($doc) {
        //add stuff to create attribute in doc. No other parameters expected
        $d = new \stdClass();
        $d->documentation = "Creates a new remote resource by executing a HTTP PUT on an URL formatted like " . Config::get("general", "hostname") . Config::get("general", "subdir") . "packagename/newresource. The base_uri needs to point to another The DataTank instance.";
        $resource = new RemoteResourceCreator("", "", array()); //make an empty object. In the end we only need a remote resource
        $d->parameters = $resource->documentParameters();
        $d->requiredparameters = $resource->documentRequiredParameters();
        if (!isset($doc->create)) {
            $doc->create = new \stdClass();
        }
        $doc->create->remote = $d;
    }

    /*
     * This object contains all the information
     * FROM the last used
     * requested object. This way we wont have to call the remote resource
     * every single call to this factory. If we receive a call
     * for another resource, we replace it by the newly asked factory.
     */

    private function fetchResourceDocumentation($package, $resource) {
        $result = DBQueries::getRemoteResource($package, $resource);
        if (sizeof($result) == 0) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($package . "/" . $resource), $exception_config);
        }
        $url = $result["url"] . "TDTInfo/Resources/" . $result["package"] . "/" . $result["resource"] . ".php";
        $options = array("cache-time" => 5); //cache for 5 seconds
        $request = Request::http($url, $options);

        if (isset($request->error)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($url), $exception_config);
        }
        $data = unserialize($request->data);
        $remoteResource = new \stdClass();

        if (!isset($remoteResource->documentation) && isset($data[$resource]) && isset($data[$resource]->documentation)) {
            $remoteResource->documentation = $data[$resource]->documentation;
        } else {
            $remoteResource->documentation = new \stdClass();
        }

        if (isset($data[$resource]->parameters)) {
            $remoteResource->parameters = $data[$resource]->parameters;
        } else {
            $remoteResource->parameters = array();
        }

        if (isset($data[$resource]->requiredparameters)) {
            $remoteResource->requiredparameters = $data[$resource]->requiredparameters;
        } else {
            $remoteResource->requiredparameters = array();
        }
        return $remoteResource;
    }

    /*
     * This object contains all the information
     * FROM the last used
     * requested object. This way we wont have to call the remote resource
     * every single call to this factory. If we receive a call
     * for another resource, we replace it by the newly asked factory.
     */

    private function fetchResourceDescription($package, $resource) {
        $result = DBQueries::getRemoteResource($package, $resource);
        if (sizeof($result) == 0) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($package . "/" . $resource), $exception_config);
        }
        $url = $result["url"] . "TDTInfo/Resources/" . $result["package"] . "/" . $result["resource"] . ".php";
        $options = array("cache-time" => 5); //cache for 5 seconds
        $request = Request::http($url, $options);

        if (isset($request->error)) {
            $exception_config = array();
            $exception_config["log_dir"] = Config::get("general", "logging", "path");
            $exception_config["url"] = Config::get("general", "hostname") . Config::get("general", "subdir") . "error";
            throw new TDTException(404, array($url), $exception_config);
        }
        $data = unserialize($request->data);
        $remoteResource = new \stdClass();
        $remoteResource->package_name = $package;
        $remoteResource->remote_package = $result["package"];
        if (!isset($remoteResource->documentation) && isset($data[$resource]) && isset($data[$resource]->documentation)) {
            $remoteResource->documentation = $data[$resource]->documentation;
        } else {
            $remoteResource->documentation = new \stdClass();
        }

        $remoteResource->resource = $resource;
        $remoteResource->base_url = $result["url"];
        $remoteResource->resource_type = "remote";
        if (isset($data[$resource]->parameters)) {
            $remoteResource->parameters = $data[$resource]->parameters;
        } else {
            $remoteResource->parameters = array();
        }

        if (isset($data[$resource]->requiredparameters)) {
            $remoteResource->requiredparameters = $data[$resource]->requiredparameters;
        } else {
            $remoteResource->requiredparameters = array();
        }
        return $remoteResource;
    }

}

?>
