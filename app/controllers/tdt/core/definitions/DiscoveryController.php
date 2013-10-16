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

        $data_result = new Data();
        $data_result->data = $dis_document;

        // Return discovery document in JSON
        return str_replace("\/", "/", json_encode($data_result->data));
    }

    /**
     * Create the discovery document.
     * TODO create the DELETE and PATCH section of the discovery document
     * TODO add more resources (definitions, ...)
     */
    private static function createDiscoveryDocument(){

        // Create and return a dument that holds a self-explanatory document
        // about how to interface with the datatank.
        $discovery_document = new \stdClass();

        // Create the discovery dument head properties.
        $discovery_document->protocol = "rest";
        $discovery_document->rootUrl = "URL TODO"; //TODO provide host uri in the configuration of core.
        $discovery_document->resources = new \stdClass();

        $definitions = new \stdClass();

        $methods = new \stdClass();
        $put = new \stdClass();
        $delete = new \stdClass();
        $patch = new \stdClass();

        $put->httpMethod = "PUT";
        $put->path = "/definitions/{identifier}";
        $put->description = "Add a resource definition identified by the {identifier} value, and of the type identified by the content type header value {mediaType}. The {identifier} consists of 1 or more collection identifiers, followed by a final resource name. (e.g. world/demography/2013/seniors)";
        $put->contentType = "application/tdt.{mediaType}";

        // Every type of definition is identified by a certain mediatype.
        $put->mediaType = new \stdClass();

        // Fetch all the supported definition models by iterating the models directory.
        if ($handle = opendir(app_path() . '/models')) {
            while (false !== ($entry = readdir($handle))) {
                if (preg_match("/(.+)Definition\.php/i", $entry, $matches)) {

                    $model = $matches[1] . "Definition";

                    $definition_type = strtolower($matches[1]);

                    if(method_exists($model, 'getCreateParameters')){

                        $put->mediaType->$definition_type = new \stdClass();
                        $put->mediaType->$definition_type->description = "Create a definition that allows for publication of data inside a $matches[1] datastructure.";
                        $put->mediaType->$definition_type->parameters = $model::getCreateParameters();
                    }
                }
            }
            closedir($handle);
        }

        // Attach the methods to the up the methods object.
        $methods->put = $put;
        $methods->delete = $delete;
        $methods->path = $patch;

        // Attach the methods to the definitions object.
        $definitions->methods = $methods;
        $discovery_document->resources->definitions = $definitions;

        return $discovery_document;
    }

}