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
    /**
     * Return a list of the available formats that the data structure can be formatted into
     *
     * @param Tdt\Core\Datasets\Data $data
     * @return array
     */
    public function getAvailableFormats($data)
    {
        return $this->getFormatsForType($data->source_definition);
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
                    $formats['Map'] = 'map';
                    $formats['KML'] = 'kml';
                    $formats['GeoJSON'] = 'geojson';
                    $formats['WKT'] = 'WKT';
                }
                elseif($source_definition['xslt_file']){
                    $formats['CSV'] = 'csv';
                    $formats['XML'] = 'xml';
                }

                else {
                    $formats['XML'] = 'xml';
                }
                break;
            case 'kml':
                $formats['Map'] = 'map';
                $formats['KML'] = 'kml';
                $formats['GeoJSON'] = 'geojson';
                $formats['WKT'] = 'WKT';
                break;
            case 'json':
                if ($source_definition['jsontype'] == 'GeoJSON') {
                    $formats['Map'] = 'map';
                    $formats['GeoJSON'] = 'geojson';
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

                if ($source_definition['query_type'] == 'construct') {
                    $formats['NT'] = 'ntriples';
                    $formats['TTL'] = 'Turtle';
                    $formats['RDF'] = 'RDF';
                    $formats['JSON-LD'] = 'JSON-LD';
                } elseif ($source_definition['query_type'] == 'select') {
                    $formats['CSV'] = 'CSV';
                    $formats['JSON'] = 'JSON';
                }
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
