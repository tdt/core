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


        // Render the view
        return \View::make('dataset.code')->with('title', 'The Datatank')
                                          ->with('body', self::displayTree($dataObj->data))
                                          ->with('definition', $dataObj->definition);
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
