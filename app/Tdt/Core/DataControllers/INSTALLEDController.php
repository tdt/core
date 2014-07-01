<?php

namespace Tdt\Core\DataControllers;

use Tdt\Core\Datasets\Data;
use Symfony\Component\HttpFoundation\Request;

/**
 * Installed Controller
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class INSTALLEDController extends ADataController
{

    public function readData($source_definition, $rest_parameters = array())
    {

        // Include the class
        $class_file = app_path() . '/../Installed/' .  $source_definition['path'];

        if (file_exists($class_file)) {
            require_once $class_file;

            $class_name = $source_definition['class'];

            // Check if class exists
            if (class_exists($class_name)) {

                $installed = new $class_name();
                $parameters = $installed->getParameters();
                $parameter_keys = array_keys($parameters);

                // REST parameters
                foreach ($rest_parameters as $param) {

                    if (!empty($param)) {
                        // Get next parameter from resource
                        $key = array_shift($parameter_keys);

                        if (!empty($key)) {
                            // Pass the parameter to the resource in right order
                            $installed->setParameter($key, $param);
                        } else {
                            break;
                        }
                    }

                }

                // Check for other required parameters
                if (!empty($parameter_keys)) {
                    foreach ($parameter_keys as $key) {
                        if (!empty($parameters[$key]['required']) && $parameters[$key]['required']) {
                            \App::abort(400, "Oops, you forgot to specify the REST parameter '$key' (" . $parameters[$key]['description'] . "). You have to specify this parameter by passing it as a part of the URI, unlike optional parameters which are passed in the query string.");
                        }
                    }
                }

                // Build data
                $data_result = new Data();
                $data_result->data = $installed->getData();

                // if the installed resource wrapped the object in a Data object themself, just return this object.
                if ($data_result->data instanceof Data) {
                    return $data_result->data;
                }

                return $data_result;

            } else {
                $class = $source_definition['class'];
                $path = $source_definition['path'];
                \App::abort(500, "Can't find the class '$class' in the file for the installed resource ($path).");
            }

        } else {
            $path = $source_definition['path'];
            \App::abort(500, "Can't find the file for the installed resource ($path).");
        }
    }
}
