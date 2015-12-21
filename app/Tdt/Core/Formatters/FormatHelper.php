<?php

namespace Tdt\Core\Formatters;

/**
 * FormatHelper helps finding available formats for a certain datastructure
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class FormatHelper
{
    private static $tabular_sources = array('csv', 'xls', 'shp');

    /**
     * Return a list of the available formats that the data structure can be formatted into
     *
     * @param Tdt\Core\Datasets\Data $data
     * @return array
     */
    public function getAvailableFormats($data)
    {
        $formats = array(
        );

        $source_type = $data->source_definition['type'];

        if (strtolower($source_type) != 'xml') {
            $formats['JSON'] = 'json';
        } else {
            $formats['XML'] = 'xml';
        }

        // Check for tabular sources
        if (in_array(strtolower($source_type), self::$tabular_sources)) {
            $formats['CSV'] = 'csv';
        }

        // Check for geographical properties
        if (!empty($data->geo)) {
            $formats = array_merge(array('Fullscreen' => 'map'), $formats);
            $formats['KML'] = 'kml';
            $formats['GeoJSON'] = 'geojson';
            $formats['WKT'] = 'wkt';
        } elseif (!empty($data->geo_formatted) && $data->geo_formatted) {
            if (strtolower($source_type) == 'xml') {
                $formats = array_merge(array('Fullscreen map' => 'map'), $formats);
                $formats['KML'] = 'kml';
                $formats['GEOJSON'] = 'geojson';
                unset($formats['XML']);
            } elseif (strtolower($source_type) == 'json' && $data->geo_formatted) {
                $formats = array_merge(array('Fullscreen map' => 'map'), $formats);
                $formats['GeoJSON'] = 'geojson';
                unset($formats['JSON']);
            }
        }

        // Check for semantic sources, identified by the data being wrapped in an EasyRdf_Graph
        if (is_object($data->data) && get_class($data->data) == 'EasyRdf_Graph') {
            $formats['JSON-LD'] = 'jsonld';
            $formats['N-Triples'] = 'nt';
            $formats['Turtle'] = 'ttl';
            $formats['RDF'] = 'xml';
            unset($formats['XML']);
        } else {
            $formats['PHP'] = 'php';
        }

        return $formats;
    }
}
