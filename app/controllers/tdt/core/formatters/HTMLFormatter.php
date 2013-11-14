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
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Content-Type', 'text/html; charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj){

        $dataset_link  = \URL::to($dataObj->definition->collection_uri . "/" . $dataObj->definition->resource_name);
        if(!empty($dataObj->rest_parameters)){
            $dataset_link .= '/' . implode('/', $dataObj->rest_parameters);
        }

        // Check if other views need to be served
        switch($dataObj->source_definition->getType()){
            case 'CSV':
                $view = 'dataset.tabular';
                $data = $dataObj->data;
                break;
            case 'LD':
                // This data object is always semantic
                $view = 'dataset.code';

                // Check if a configuration is given
                $conf = array();
                if(!empty($dataObj->semantic->conf)){
                    $conf = $dataObj->semantic->conf;
                }

                // Serializer instantiation
                $ser = \ARC2::getRDFJSONSerializer($conf);

                // Use ARC to serialize to JSON (override)
                $data = self::displayTree(json_decode($ser->getSerializedTriples($dataObj->data->getTriples())));

                break;
            default:
                $view = 'dataset.code';
                $data = self::displayTree($dataObj->data);
                break;
        }

        // Render the view
        return \View::make($view)->with('title', 'The Datatank')
                                          ->with('body', $data)
                                          ->with('definition', $dataObj->definition)
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

        $formattedJSON = self::prettyPrint(json_encode($data));

        return str_replace("\/","/", $formattedJSON);
    }

    private static function prettyPrint($json){
        $result = '';
        $level = 0;
        $prev_char = '';
        $in_quotes = false;
        $ends_line_level = NULL;
        $json_length = strlen( $json );

        for( $i = 0; $i < $json_length; $i++ ) {
            $char = $json[$i];

            $new_line_level = NULL;
            $post = "";
            if( $ends_line_level !== NULL ) {
                $new_line_level = $ends_line_level;
                $ends_line_level = NULL;
            }
            if( $char === '"' && $prev_char != '\\' ) {
                $in_quotes = !$in_quotes;
            } else if( ! $in_quotes ) {
                switch( $char ) {
                    case '}': case ']':
                        $level--;
                        $ends_line_level = NULL;
                        $new_line_level = $level;
                        break;

                    case '{': case '[':
                        $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ": case "\t": case "\n": case "\r":
                        $char = "";
                        $ends_line_level = $new_line_level;
                        $new_line_level = NULL;
                        break;
                }
            }
            if( $new_line_level !== NULL ) {
                $result .= "\n".str_repeat( "    ", $new_line_level );
            }
            $result .= $char.$post;
            $prev_char = $char;
        }

        return $result;
    }

}
