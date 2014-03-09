<?php

namespace tdt\core\datacontrollers;

use tdt\core\cache\Cache;
use tdt\core\datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * JSON Controller
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class JSONController extends ADataController
{

    public function readData($source_definition, $rest_parameters = array())
    {

        $uri = $source_definition['uri'];

        // Check for caching
        if (Cache::has($uri)) {
            $data = Cache::get($uri);
        }else{
            // Fetch the data
            $data =@ file_get_contents($uri);
            if ($data) {
                Cache::put($uri, $data, $source_definition['cache']);
            }else{
                $uri = $source_definition['uri'];
                \App::abort(500, "Cannot retrieve data from the JSON file located on $uri.");
            }
        }

        $php_object = json_decode($data);

        $data_result = new Data();
        $data_result->data = $php_object;
        return $data_result;
    }
}
