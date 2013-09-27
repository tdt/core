<?php

namespace tdt\core\datasets;

class DatasetController extends \Controller {

    public static function handle($uri){

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

                // TODO: format (via formatters with negotiation)
                $formatter_class = '\\tdt\\core\\formatters\\'.$definition->source_type.'Formatter';

                // Return the formatted response
                return $formatter_class::createResponse($data);

            }else{
                \App::abort(404, "Source for the definition could not be found.");
            }

        }else{
            \App::abort(404, "The resource you were looking for could not be found (URI: $uri).");
        }

    }

}