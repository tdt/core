<?php

namespace Tdt\Core\Formatters;

/**
 * FormatHelper helps finding available formats for a certain data source type
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

        $source_type = strtolower($data->source_definition['type']);

        if ($source_type != 'xml' && $source_type != 'kml') {
            $formats['JSON'] = 'json';
        } elseif ($source_type == 'xml') {
            $formats['XML'] = 'xml';
        }

        // Check for tabular sources
        if (in_array($source_type, self::$tabular_sources)) {
            $formats['CSV'] = 'csv';
        }

        // Check for geographical properties
        if (!empty($data->geo)) {
            $formats = array_merge(array('Fullscreen' => 'map'), $formats);
            $formats['KML'] = 'kml';
            $formats['GeoJSON'] = 'geojson';
            $formats['WKT'] = 'wkt';
        } elseif (!empty($data->geo_formatted) && $data->geo_formatted) {
            if ($source_type == 'kml') {
                $formats = array_merge(array('Fullscreen map' => 'map'), $formats);
                $formats['KML'] = 'kml';
                $formats['GEOJSON'] = 'geojson';
                unset($formats['XML']);
            } elseif ($source_type == 'json' && $data->geo_formatted) {
                $formats = array_merge(array('Fullscreen map' => 'map'), $formats);
                $formats['GeoJSON'] = 'geojson';
                unset($formats['JSON']);
            }
        }

        // Check for semantic sources, identified by the data being wrapped in an EasyRdf_Graph
        if (is_object($data->data) && get_class($data->data) == 'EasyRdf\Graph') {
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

    /**
     * Get the available formats based on the type of data source
     * This can differ from the actual available formats (e.g. a SPARQL query can return a results
     * from a construct or a select query, which can or can not be - respectively - be formatted in semantic formats)
     *
     * @param array $source_definition
     *
     * @return array
     */
    public function getFormatsForType($source_definition)
    {
        $formats = [];

        $source_type = strtolower($source_definition['type']);

        switch ($source_type) {
            case 'xml':
                if ($source_definition['geo_formatted']) {
                    $formats['KML'] = 'kml';
                    $formats['GeoJSON'] = 'geojson';
                } else {
                    $formats['XML'] = 'xml';
                }
                break;
            case 'json':
                if ($source_definition['jsontype'] == 'GeoJSON') {
                    $formats['GeoJSON'] = 'geojson';
                    $formats['map'] = 'map';
                } elseif ($source_definition['jsontype'] == 'JSON-LD') {
                    $formats['JSON-LD'] = 'jsonld';
                } else {
                    $formats['JSON'] = 'json';
                }
                break;
            case 'shp':
                $formats['Map'] = 'map';
                $formats['GeoJSON'] = 'geojson';
                $formats['KML'] = ' ';
                $formats['WKT'] = 'WKT';
                $formats['CSV'] = 'csv';
                break;
            case 'mongo':
                $formats['JSON'] = 'json';
                break;
            case 'elasticsearch':
                $formats['JSON'] = 'json';
                break;
            case 'rdf':
                break;
            case 'sparql':
                $formats['JSON'] = 'json';
                break;
            case 'xls':
                $formats['JSON'] = 'json';
                $formats['CSV'] = 'csv';
                break;
            case 'mysql':
                $formats['JSON'] = 'json';
                $formats['CSV'] = 'csv';
                break;
            case 'csv':
                $formats['JSON'] = 'json';
                $formats['CSV'] = 'csv';
                break;
        }

        return $formats;
    }
}
