<?php

namespace tdt\core\datacontrollers;

use tdt\core\datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * JSON Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class JSONController extends ADataController {

    public function readData($source_definition, $rest_parameters = array()){

        $data = file_get_contents($source_definition->uri);

        if($data){

            $php_object = json_decode($data);

            $data_result = new Data();
            $data_result->data = $php_object;
            return $data_result;
        }

        \App::abort(404, "Cannot retrieve data from the JSON file located on $source_definition->uri.");
    }
}
