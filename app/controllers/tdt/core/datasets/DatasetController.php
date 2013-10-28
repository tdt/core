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

    private static $PAGING_KEYWORDS = array('next', 'last', 'previous', 'first');

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

                // Create parameters array
                $parameters = array();

                // Get REST parameters
                $rest_parameters = str_replace($definition->collection_uri . '/' . $definition->resource_name, '', $uri);
                $rest_parameters = ltrim($rest_parameters, '/');

                if(strlen($rest_parameters) > 0){
                    $parameters = explode('/', $rest_parameters);
                }

                // Retrieve dataobject from datacontroller
                $data = $data_controller->readData($source_definition, $parameters);

                // Add definition to the object
                $data->definition = $definition;

                // Add source definition to the object
                $data->source_definition = $source_definition;

                // Add the paging headers if necessary.
                if(!empty($data->paging)){
                    self::addPagingHeaders($data->paging, $extension);
                }

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
     * Provide paging headers in the response using the Link HTTP header.
     */
    private static function addPagingHeaders($paging, $extension){

        $link_value = '';
        $base_uri = \Request::url();

        // Chip of the extension which indicates formatter we should use, in order to form the base uri.
        $base_uri = substr($base_uri, 0, strlen($base_uri) - strlen('.' . $extension));

        foreach($paging as $keyword => $page_info){

            if(!in_array($keyword, self::$PAGING_KEYWORDS)){

                $key_words = implode(', ', self::$PAGING_KEYWORDS);
                \App::abort(452, "The given paging keyword, $keyword, has not been found. Supported keywords are $key_words.");

            }else if(count($page_info) != 2){
                \App::abort(452, "The provided page info did not contain 2 parts, it should only contain a page number and a page size.");
            }

            $link_value .= $base_uri . '.' . $extension . '?page=' . $page_info[0] . '&page_size=' . $page_info[1] . ';rel=' . $keyword . ',';
        }

        // Trim the most right comma off.
        $link_value = rtrim($link_value, ",");
        // Set the paging header.
        header("Link: " . $link_value);

    }
}
