<?php

namespace Tdt\Core\Formatters;

use Tdt\Core\Formatters\KMLFormatter;
use Symm\Gisconverter\Gisconverter;

/**
 * GEOJson Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class GEOJSONFormatter implements IFormatter
{

    private static $definition;

    public static function createResponse($dataObj)
    {
        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'application/vnd.geo+json');

        return $response;
    }

    public static function getBody($dataObj)
    {
        // Get the KML and transform it to GeoJSON
        $kml = KMLFormatter::getBody($dataObj);

        return Gisconverter::kmlToGeojson($kml);
    }

    public static function getDocumentation()
    {
        return "Returns a GeoJSON document.";
    }
}
