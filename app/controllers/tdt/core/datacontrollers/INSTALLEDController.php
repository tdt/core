<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * JSON Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class INSTALLEDController extends ADataController {

    public function readData($source_definition, $rest_parameters = null){

        // Include the class
        $class_file = app_path() . '/../installed/' .  $source_definition->path;

        if(file_exists($class_file)){
            require_once $class_file;

            $class_name = $source_definition->class;

            // Check if class exists
            if(class_exists($class_name)){

                $installed = new $class_name();
                $parameters = $installed->getParameters();
                $parameter_keys = array_keys($parameters);

                // REST parameters
                foreach($rest_parameters as $param){

                    if(!empty($param)){
                        // Get next parameter from resource
                        $key = array_shift($parameter_keys);

                        if(!empty($key)){
                            // Pass the parameter to the resource in right order
                            $installed->setParameter($key, $param);
                        }else{
                            break;
                        }
                    }

                }

                // Check for other required parameters
                if(!empty($parameter_keys)){
                    foreach($parameter_keys as $key){
                        if(!empty($parameters[$key]['required']) && $parameters[$key]['required']){
                            \App::abort(400, "Oops, you forgot to specify the REST parameter '$key' (".$parameters[$key]['description'].").");
                        }
                    }
                }

                // Build data
                $data_result = new Data();
                $data_result->data = $installed->getData();
                return $data_result;


            }else{
                \App::abort(400, "Can't find the class '$source_definition->class' in the file for the installed resource ($source_definition->path).");
            }

        }else{
            \App::abort(400, "Can't find the class for the installed resource ($source_definition->path).");
        }
    }
}