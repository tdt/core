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

        // Query parameters
        $query_string = '';
        if(!empty($_GET)){
            $query_string = '?' . http_build_query(\Input::all());
        }

        // Links to pages
        $prev_link = '';
        $next_link = '';
        if(!empty($dataObj->paging)){
            $input_array = array_except(\Input::all(), array('limit', 'offset'));

            if(!empty($paging['previous'])){
                $prev_link = '?' . http_build_query($input_array) . '&offset=' . $dataObj->paging['previous'][0] . '&limit=' . $dataObj->paging['previous'][1];
            }

            if(!empty($paging['next'])){
                $next_link = '?' . http_build_query($input_array) . '&offset=' . $dataObj->paging['next'][0] . '&limit=' . $dataObj->paging['next'][1];
            }
        }

        if($dataObj->is_spectql){
            // SpectQL result

            // Create the link to the dataset
            $dataset_link  = \URL::to('spectql/' . $dataObj->definition->collection_uri . "/" . $dataObj->definition->resource_name);

            // Append rest parameters
            if(!empty($dataObj->rest_parameters)){
                $dataset_link .= implode('/', $dataObj->rest_parameters);
                $dataset_link = substr($dataset_link, 0, -5);
            }

            $view = 'dataset.spectql';
            $data = self::displayTree($dataObj->data);

        }else{
            // Create the link to the dataset
            $dataset_link  = \URL::to($dataObj->definition->collection_uri . "/" . $dataObj->definition->resource_name);

            // Append rest parameters
            if(!empty($dataObj->rest_parameters)){
                $dataset_link .= '/' . implode('/', $dataObj->rest_parameters);
            }

            if(!empty($dataObj->source_definition)){

                // Check if other views need to be served
                switch($dataObj->source_definition->getType()){
                    case 'XLS':
                    case 'CSV':
                        $view = 'dataset.tabular';
                        $data = $dataObj->data;

                        break;
                    case 'SHP':
                        $view = 'dataset.map';
                        $data = $dataset_link . '.map' . $query_string;

                        break;

                    default:
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
                }

            }else{
                // Collection view
                $view = 'dataset.collection';
                $data = $dataObj->data;
            }

        }

        // Render the view
        return \View::make($view)->with('title', 'The Datatank')
                                 ->with('body', $data)
                                 ->with('definition', $dataObj->definition)
                                 ->with('paging', $dataObj->paging)
                                 ->with('source_definition', $dataObj->source_definition)
                                 ->with('dataset_link', $dataset_link)
                                 ->with('prev_link', $prev_link)
                                 ->with('next_link', $next_link)
                                 ->with('query_string', $query_string);
    }

    public static function getDocumentation(){
        return "The HTML formatter is a formatter which prints output for humans. It prints everything in the internal object and extra links towards meta-data and documentation.";
    }

    private static function displayTree($data) {

        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if(defined('JSON_PRETTY_PRINT')){
            $formattedJSON = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }else {
            $formattedJSON = json_encode($data);
        }

        return str_replace("\/","/", $formattedJSON);
    }
}
