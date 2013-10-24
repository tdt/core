<?php

namespace tdt\core\formatters;

/**
 * JSON Formatter
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class JSONFormatter implements IFormatter{

    public static function createResponse($dataObj){

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj){

        // Build the body
        $body = $dataObj->data;
        if (is_object($dataObj->data)) {
            $body = get_object_vars($dataObj->data);
        }

        if($dataObj->is_semantic){

            // Serializer instantiation
            $ser = \ARC2::getRDFJSONSerializer();

            // Use ARC to serialize to JSON (override)
            return $ser->getSerializedTriples($dataObj->data->getTriples());

        }

        // Unescape slashes
        return str_replace("\/", "/", json_encode($body));
    }

    public static function getDocumentation(){
        return "A JSON formatter.";
    }

}
