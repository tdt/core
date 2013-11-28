<?php

namespace tdt\core\definitions;

use tdt\core\auth\Auth;
use tdt\core\datasets\Data;
use tdt\core\ContentNegotiator;

/**
 * DiscoveryController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class DiscoveryController extends \Controller {

    public static function handle($uri){

        // Set permission
        Auth::requirePermissions('discovery.view');

        // Propagate the request based on the HTTPMethod of the request
        $method = \Request::getMethod();

        switch($method){
            case "GET":
                $discovery_document = self::createDiscoveryDocument();

                // If the input package is installed, add it to the discovery document
                if(class_exists('tdt\input\controllers\DiscoveryController')){
                    $discovery_class = 'tdt\input\controllers\DiscoveryController';
                    $discovery_document->resources->input = $discovery_class::createDiscoveryDocument();
                }

                return self::makeResponse(str_replace("\/", "/", json_encode($discovery_document)));

                break;
            default:
                // Method not supported
                \App::abort(405, "The HTTP method '$method' is not supported by this resource.");
                break;
        }
    }

    /**
     * Create the discovery document
     */
    private static function createDiscoveryDocument(){

        // Create and return a dument that holds a self-explanatory document
        // about how to interface with the datatank
        $discovery_document = new \stdClass();

        // Create the discovery dument head properties
        $discovery_document->protocol = "rest";
        $discovery_document->rootUrl = \Request::root() . '/api';
        $discovery_document->resources = new \stdClass();

        $discovery_document->resources->definitions = self::createDefinitions();
        $discovery_document->resources->info = self::createInfo();
        $discovery_document->resources->dcat = self::createDcat();

        return $discovery_document;
    }

    /**
     * Create the definitions resource for the discovery document
     */
    private static function createDefinitions(){

        $definitions = new \stdClass();

        $methods = new \stdClass();

        // Attach the methods to the up the methods object
        $methods->get = self::createDefGetDiscovery();
        $methods->put = self::createDefPutDiscovery();
        $methods->delete = self::createDefDeleteDiscovery();
        $methods->patch = self::createDefPatchDiscovery();

        // Attach the methods to the definitions object
        $definitions->methods = $methods;

        return $definitions;
    }

    /**
     * Create the get discovery documentation.
     */
    private static function createDefGetDiscovery(){

        $get = new \stdClass();

        $get->httpMethod = "GET";
        $get->path = "/definitions/{identifier}";
        $get->description = "Get a resource definition identified by the {identifier} value, or retrieve a list of the current definitions by leaving {identifier} empty.";

        return $get;
    }

    /**
     * Create the put discovery documentation.
     */
    private static function createDefPutDiscovery(){

        $put = new \stdClass();

        $put->httpMethod = "PUT";
        $put->path = "/definitions/{identifier}";
        $put->description = "Add a resource definition identified by the {identifier} value, and of the type identified by the content type header value {mediaType}. The {identifier} consists of 1 or more collection identifiers, followed by a final resource name. (e.g. world/demography/2013/seniors)";
        $put->contentType = "application/tdt.{mediaType}";

        // Every type of definition is identified by a certain mediatype
        $put->mediaType = new \stdClass();

        // Get the base properties that can be added to every definition
        $base_properties = \Definition::getCreateParameters();

        // Fetch all the supported definition models by iterating the models directory
        if ($handle = opendir(app_path() . '/models/sourcetypes')) {
            while (false !== ($entry = readdir($handle))) {

                if (preg_match("/(.+)Definition\.php/i", $entry, $matches)) {

                    $model = ucfirst(strtolower($matches[1])) . "Definition";

                    $definition_type = strtolower($matches[1]);

                    if(method_exists($model, 'getAllParameters')){

                        $put->mediaType->$definition_type = new \stdClass();
                        $put->mediaType->$definition_type->description = "Create a definition that allows for publication of data inside a $matches[1] datastructure.";

                        $all_properties = array_merge($model::getAllParameters(), $base_properties);
                        // Fetch the Definition properties, and the SourceType properties, the latter also contains relation properties e.g. TabularColumn properties
                        $put->mediaType->$definition_type->parameters = $all_properties;
                    }
                }
            }
            closedir($handle);
        }

        return $put;
    }

    /**
     * Create the delete discovery documentation.
     */
    private static function createDefDeleteDiscovery(){

        $delete = new \stdClass();

        $delete->httpMethod = "DELETE";
        $delete->path = "/definitions/{identifier}";
        $delete->description = "Delete a resource definition identified by the {identifier} value.";

        return $delete;
    }

    /**
     * Create the patch discovery documentation.
     */
    private static function createDefPatchDiscovery(){

        $patch = new \stdClass();

        $patch->httpMethod = "PATCH";
        $patch->path = "/definitions/{identifier}";
        $patch->description = "Patch a resource definition identified by the {identifier} value. In contrast to PUT, there's no need to pass the media type in the headers.";

        // Every type of definition is identified by a certain mediatype (source type)
        $patch->mediaType = new \stdClass();

        // Get the base properties that can be added to every definition
        $base_properties = \Definition::getCreateParameters();

        // Fetch all the supported definition models by iterating the models directory
        if ($handle = opendir(app_path() . '/models/sourcetypes')) {
            while (false !== ($entry = readdir($handle))) {

                if (preg_match("/(.+)Definition\.php/i", $entry, $matches)) {

                    $model = ucfirst(strtolower($matches[1])) . "Definition";

                    $definition_type = strtolower($matches[1]);

                    if(method_exists($model, 'getAllParameters')){

                        $patch->mediaType->$definition_type = new \stdClass();
                        $patch->mediaType->$definition_type->description = "Patch an existing definition.";

                        $all_properties = array_merge($model::getAllParameters(), $base_properties);

                        foreach($all_properties as $key => $info){
                            unset($all_properties[$key]['required']);
                        }

                        // Fetch the Definition properties, and the SourceType properties, the latter also contains relation properties e.g. TabularColumn properties
                        $patch->mediaType->$definition_type->parameters = $all_properties;
                    }
                }
            }
            closedir($handle);
        }

        return $patch;
    }

    /**
     * Create the info discovery documentation
     */
    private static function createInfo(){

        // Info only supports the get method
        $info = new \stdClass();

        // Attach the methods to the info object
        $info->methods = new \stdClass();
        $info->methods->get  = new \stdClass();

        $info->methods->get->httpMethod = "GET";
        $info->methods->get->path = "/info";
        $info->methods->get->description = "Get a list of all retrievable datasets published on this datatank instance.";

        return $info;
    }

    /**
     * Create the dcat discovery documentation
     */
    private static function createDcat(){

        // Dcat only supports the get method
        $dcat = new \stdClass();

        // Attach the methods to the dcat object
        $dcat->methods = new \stdClass();
        $dcat->methods->get  = new \stdClass();

        $dcat->methods->get->httpMethod = "GET";
        $dcat->methods->get->path = "/dcat";
        $dcat->methods->get->description = "Get a list of all retrievable datasets published on this datatank instance in a DCAT vocabulary. In contrast with all the other resources, this data will be returned in a turtle serialization.";

        return $dcat;
    }

    /**
     * Return the response with the given data ( formatted in json )
     */
    private static function makeResponse($data){

         // Create response
        $response = \Response::make($data, 200);

        // Set headers
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
}
