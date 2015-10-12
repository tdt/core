<?php

namespace Tdt\Core\Formatters;

use Tdt\Core\Formatters\KMLFormatter;
use Symm\Gisconverter\Gisconverter;

/**
 * KWT Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenlandt <jan@okfn.be>
 * @author Pieter Colpaert   <pieter@irail.be>
 */
class WKTFormatter implements IFormatter
{

    private static $definition;

    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/plain');

        return $response;
    }

    public static function getBody($dataObj)
    {
        $kml = KMLFormatter::getBody($dataObj);

        return Gisconverter::kmlToWkt($kml);
    }

    public static function getDocumentation()
    {
        return "Returns a KWT document.";
    }
}
