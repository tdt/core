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
            echo $source_definition->uri;


        }else{
            \App::abort(404, "The resource you were looking for could not be found (URI: $uri).");
        }

    }

}