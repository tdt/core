<?php

namespace tdt\core\datasets;

class DatasetController extends \Controller {

    public static function handle($uri){


        // Get definition
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name, '%')", array($uri))->first();

        if($definition){

            // Create source class
            $source_class = $definition->source_type.'Definition';

            // echo $source_class; die();

            // Get source definition
            $source_definition = $source_class::where('id', $definition->source_id)->first();

            if($source_definition){

                $data_controller = new \tdt\core\datacontrollers\CSVController();
                $data_controller->readData($source_definition);

            }else{
                \App::abort(404, "Source for the definition could not be found.");
            }


        }else{
            \App::abort(404, "The resource you were looking for could not be found (URI: $uri).");
        }

    }

}