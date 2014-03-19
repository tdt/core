<?php

namespace Tdt\Core\Formatters;

/**
 * JSON-LD Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class JSONLDFormatter implements IFormatter
{

    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'application/ld+json;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {

        if ($dataObj->is_semantic) {

            // Check if a configuration is given
            $conf = array();
            if (!empty($dataObj->semantic->conf)) {
                $conf = $dataObj->semantic->conf;
            }

            return $dataObj->data->serialise('jsonld');
        } else {
            \App::abort(400, "The data is not a semantically linked document, a linked data JSON representation is not possible.");
        }

    }

    public static function getDocumentation()
    {
        return "A JSON-LD formatter.";
    }
}
