<?php

namespace Tdt\Core\Formatters;

/**
 * JSON Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class JSONFormatter implements IFormatter
{

    public static function createResponse($dataObj)
    {

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {
        // If the data is semantic, return json ld
        if ($dataObj->is_semantic) {
            $jsonld_formatter = new JSONLDFormatter();

            return $jsonld_formatter->getBody($dataObj);
        }

        // Build the body
        $body = $dataObj->data;
        if (is_object($dataObj->data)) {
            $body = get_object_vars($dataObj->data);
        }

        if ($dataObj->is_semantic) {
            // Check if a configuration is given
            $conf = array();
            if (!empty($dataObj->semantic->conf)) {
                $conf = $dataObj->semantic->conf;
            }

            return $dataObj->data->serialise('json');
        }

        // Unescape slashes
        return str_replace("\/", "/", json_encode($body));
    }

    public static function getDocumentation()
    {
        return "A JSON formatter.";
    }
}
