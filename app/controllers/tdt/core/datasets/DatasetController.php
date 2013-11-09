<?php

namespace tdt\core\datasets;

use tdt\core\ContentNegotiator;
use tdt\core\definitions\DefinitionController;

/**
 * DatasetController
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class DatasetController extends \Controller {

    public static function handle($uri){

        // Split for an (optional) extension
        preg_match('/([^\.]*)(?:\.(.*))?$/', $uri, $matches);

        // URI is always the first match
        $uri = $matches[1];

        // Get extension (if set)
        $extension = (!empty($matches[2]))? $matches[2]: null;

        // Get definition
        $definition = DefinitionController::get($uri);

        if($definition){

            // Get source definition
            $source_definition = $definition->source()->first();

            if($source_definition){

                // Create the right datacontroller
                $controller_class = '\\tdt\\core\\datacontrollers\\' . $source_definition->getType() . 'Controller';
                $data_controller = new $controller_class();

                // Get REST parameters
                $rest_parameters = str_replace($definition->collection_uri . '/' . $definition->resource_name, '', $uri);
                $rest_parameters = ltrim($rest_parameters, '/');
                $rest_parameters = explode('/', $rest_parameters);
                if(empty($rest_parameters[0])){
                    $rest_parameters = array();
                }

                // Retrieve dataobject from datacontroller
                $data = $data_controller->readData($source_definition, $rest_parameters);
                $data->rest_parameters = $rest_parameters;

                // REST filtering
                if($source_definition->getType() != 'INSTALLED' && count($data->rest_parameters) > 0){
                    $data->data = self::applyRestFilter($data->data, $data->rest_parameters);
                }

                // Add definition to the object
                $data->definition = $definition;

                // Add source definition to the object
                $data->source_definition = $source_definition;

                // Return the formatted response with content negotiation
                return ContentNegotiator::getResponse($data, $extension);
            }else{
                \App::abort(404, "Source for the definition could not be found.");
            }

        }else{
            \App::abort(404, "The resource you were looking for could not be found (URI: $uri).");
        }
    }

    /**
     * Apply RESTful filtering of the data.
     * @return mixed filtered object
     */
    private static function applyRestFilter($data, $rest_params){

        foreach($rest_params as $rest_param){

            if(is_object($data) && isset($data->$rest_param)){

                $data = $data->$rest_param;
            }elseif(is_array($data) && isset($data[$rest_param])){

                $data = $data[$rest_param];
            }else{
                \App::abort(452, "No property ($rest_param) has been found.");
            }
        }

        return array($data);

    }
}
