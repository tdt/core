<?php

namespace tdt\core\datasets;

class DatasetController extends \Controller {

    public static function handle($uri){


        // Get definition
        $definition = \Definition::whereRaw("? like CONCAT(collection_uri, '/', resource_name, '%')", array($uri))->first();

        if($definition){

            // Get source definition


        }else{
            \App::abort(404, "The resource you were looking for could not be found (URI: $uri).");
        }

    }

}