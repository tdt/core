<?php

namespace Tdt\Core\Formatters;

use Request;

/**
 * Map Formatter
 *
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 * @author Jan Vansteenladnt <jan@okfn.be>
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
        $type = 'geojson';

        if ($dataObj->definition['source_type'] == 'XmlDefinition' && $dataObj->source_definition['geo_formatted'] == 1) {
            $url = preg_replace('/\.([^\.]*)$/m', '.kml', $url);
            $type = 'kml';
        } else {
            $query_string = '';

            foreach (Request::query() as $key => $val) {
                $query_string .= "&$key=$val";
            }

            if (!empty($query_string)) {
                $query_string = ltrim($query_string, '&');
                $query_string = '?' . $query_string;
            }

            $url = preg_replace('/\.([^\.]*)$/m', '.geojson', $url);
            $url .= $query_string;
        }

        // Set the correct scheme, rely on the native PHP methods and some other parameters in order to figure it out
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";

        if ($protocol == 'https' && substr($url, 0, 5) == 'http:') {
            $url = 'https://' . substr($url, 7);
        }

        $resource = $dataObj->definition['collection_uri'] . "/" . $dataObj->definition['resource_name'];

        // Render the view
        return \View::make('layouts.map')->with('title', 'Dataset: ' . $resource . ' map | The Datatank')
                                          ->with('url', $url)
                                          ->with('type', $type);
    }

    public static function getDocumentation()
    {
        return "The map visualization creates a map based on the geographic data available in the datasource.";
    }
}
