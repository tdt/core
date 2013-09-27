<?php

namespace tdt\core\datasets;

use tdt\core\ContentNegotiator;

class DatasetController extends \Controller {

    public static function handle($uri){

        // Split extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);
        // URI is always the first match
        $uri = $matches[1];
        // Get extension (if set)
        $extension = (!empty($matches[1]))? strtoupper($matches[2]): null;

        // Get definition
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name, '%')", array($uri))->first();

        if($definition){

            // Create source class
            $source_class = $definition->source_type.'Definition';

            // Get source definition
            $source_definition = $source_class::where('id', $definition->source_id)->first();


            if($source_definition){

                // Create the right datacontroller
                $controller_class = '\\tdt\\core\\datacontrollers\\'.$definition->source_type.'Controller';
                $data_controller = new $controller_class();

                // Retrieve dataobject from datacontroller
                $data = $data_controller->readData($source_definition);

                // Return the formatted response with content negotiation
                return ContentNegotiator::getResponse($data, $extension);

            }else{
                \App::abort(404, "Source for the definition could not be found.");
            }

        }else{
            \App::abort(404, "The resource you were looking for could not be found (URI: $uri).");
        }

    }

}