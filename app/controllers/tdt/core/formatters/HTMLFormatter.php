<?php

namespace tdt\core\formatters;

/**
 * HTML Formatter
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class HTMLFormatter implements IFormatter{

    public static function createResponse($dataObj){

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/html; charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj){

        $dataset_link  = \URL::to($dataObj->definition->collection_uri . "/" . $dataObj->definition->resource_name);
        if(!empty($dataObj->rest_parameters)){
            $dataset_link .= '/' . implode('/', $dataObj->rest_parameters);
        }

        if(!empty($dataObj->source_definition)){
            // Check if other views need to be served
            switch($dataObj->source_definition->getType()){
                case 'XLS':
                case 'CSV':
                    if(empty($dataObj->rest_parameters)){
                        $view = 'dataset.tabular';
                        $data = $dataObj->data;
                    }else{
                        $view = 'dataset.code';
                        $data = self::displayTree($dataObj->data);
                    }
                    break;
                case 'SHP':
                    $view = 'dataset.map';
                    $data = $dataset_link . '.map';
                    break;

                case 'SPARQL':

                    if($dataObj->is_semantic){

                        // This data object is always semantic
                        $view = 'dataset.code';

                        // Check if a configuration is given
                        $conf = array();
                        if(!empty($dataObj->semantic->conf)){
                            $conf = $dataObj->semantic->conf;
                        }

                        // Serializer instantiation
                        $ser = \ARC2::getTurtleSerializer($conf);

                        // Serialize a triples array
                        $data = $ser->getSerializedTriples($dataObj->data->getTriples());

                    }else{

                        $view = 'dataset.code';
                        $data = self::displayTree($dataObj->data);

                    }

                    break;

                default:
                    $view = 'dataset.code';
                    $data = self::displayTree($dataObj->data);
                    break;
            }
        }else{

            // Collection view
            $view = 'dataset.collection';
            $data = $dataObj->data;
        }

        // Render the view
        return \View::make($view)->with('title', 'The Datatank')
                                          ->with('body', $data)
                                          ->with('definition', $dataObj->definition)
                                          ->with('paging', $dataObj->paging)
                                          ->with('source_definition', $dataObj->source_definition)
                                          ->with('dataset_link', $dataset_link);
    }

    public static function getDocumentation(){
        return "The HTML formatter is a formatter which prints output for humans. It prints everything in the internal object and extra links towards meta-data and documentation.";
    }

    private static function displayTree($data) {

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        $formattedJSON = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return str_replace("\/","/", $formattedJSON);
    }
}
