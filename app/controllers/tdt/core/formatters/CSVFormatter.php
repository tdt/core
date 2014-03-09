<?php

namespace tdt\core\formatters;

/**
 * CSV Formatter
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class CSVFormatter implements IFormatter
{

    public static function createResponse($dataObj){

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/csv;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj){

        // Only tabular data is allowed
        if (!is_array($dataObj->data)) {
            \App::abort(400, "You can only request a CSV formatter on a tabular datastructure.");
        }

        // Build the body
        $body = '';

        $header_printed = false;
        foreach ($dataObj->data as $row) {
            if (is_object($row)) {
               $row = get_object_vars($row);
            }else if(!is_array($row)){
                $body .= $row . "\n";
                continue;
            }

            // Print header
            if (!$header_printed) {
                $i = 0;
                foreach ($row as $key => $value) {
                    $body .= CSVFormatter::enclose($key);
                    $body .= sizeof($row)-1 != $i ? ";" : "\n";
                    $i++;
                }
                $header_printed = true;
            }

            $i = 0;
            foreach ($row as $element) {

                if (is_object($element)) {
                    if (isset($element->id)) {
                        $body .= $element->id;
                    }else if(isset($element->name)){
                        $body .= $element->name;
                    }else{
                        $body .= "n/a";
                    }
                }
                elseif(is_array($element)){
                    if (isset($element["id"])) {
                        $body .= $element["id"];
                    }else if(isset($element["name"])){
                        $body .= $element["name"];
                    }else{
                        $body .= "n/a";
                    }
                }
                else{
                    $body .= CSVFormatter::enclose($element);
                }
                $body .= sizeof($row)-1 != $i ? ";" : "\n";
                $i++;
            }
        }

        return $body;
    }

    public static function getDocumentation(){
        return "A CSV formatter. Works only on tabular data.";
    }


    /**
     * Encloses the $element in double quotes.
     */
    private static function enclose($element){
        $element = rtrim($element, '"');
        $element = ltrim($element, '"');
        $element = '"'.$element.'"';
        return utf8_encode($element);
    }

}
