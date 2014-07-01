<?php

namespace Tdt\Core\Formatters;

/**
 * Map Formatter
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class MAPFormatter implements IFormatter
{

    public static function createResponse($dataObj)
    {

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Content-Type', 'text/html; charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj)
    {

        // Parse a kml from the objectToPrint, and convert it to a geojson
        ob_start();

        echo KMLFormatter::getBody($dataObj);

        $kml = ob_get_contents();

        ob_end_clean();

        // Render the view
        return \View::make('layouts.map')->with('title', 'Dataset: ' . $dataObj->definition['collection_uri'] . "/" . $dataObj->definition['resource_name'] . ' map | The Datatank')
                                          ->with('kml', $kml);
    }

    public static function getDocumentation()
    {
        return "The map visualization creates a map based on the geographic data available in the datasource.";
    }
}
