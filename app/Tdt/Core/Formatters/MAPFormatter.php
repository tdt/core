<?php

namespace Tdt\Core\Formatters;

use Request;

/**
 * Map Formatter
 *
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
        $url = Request::url();
        $url = preg_replace('/\.([^\.]*)$/m', '.kml', $url);

        /*if (substr($url, -7) != '.kml') {
            $url .= '.kml';
        }*/

        $resource = $dataObj->definition['collection_uri'] . "/" . $dataObj->definition['resource_name'];

        // Render the view
        return \View::make('layouts.map')->with('title', 'Dataset: ' . $resource . ' map | The Datatank')
                                          ->with('url', $url);
    }

    public static function getDocumentation()
    {
        return "The map visualization creates a map based on the geographic data available in the datasource.";
    }
}
