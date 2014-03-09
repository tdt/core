<?php

namespace tdt\core\formatters;

/**
 * JSON Formatter
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class PHPFormatter implements IFormatter
{

    public static function createResponse($dataObj)
    {

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/plain;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {

        // Build the body
        $body = $dataObj->data;
        if (is_object($body)) {
            $body = get_object_vars($body);
        }

        // Unescape slashes
        return serialize($body);
    }

    public static function getDocumentation()
    {
        return "Prints data in serialized PHP object notation.";
    }
}
