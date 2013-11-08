<?php

namespace tdt\core\definitions;

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

        $dis_document = self::createDiscoveryDocument();

        // If the input package is installed, add it to the discovery document
        if(class_exists('tdt\input\controllers\DiscoveryController')){
            $discovery_class = 'tdt\input\controllers\DiscoveryController';
            $dis_document->resources->input = $discovery_class::createDiscoveryDocument();
        }

        $data_result = new Data();
        $data_result->data = $dis_document;

        // Return discovery document in JSON
        return str_replace("\/", "/", json_encode($data_result->data));
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
        $discovery_document->rootUrl = \Request::root();
        $discovery_document->resources = new \stdClass();

        $discovery_document->resources->definitions = self::createDefinitions();
        $discovery_document->resources->info = self::createInfo();

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

                        $all_properties = array_merge($base_properties, $model::getAllParameters());
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
        return null;
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
}
