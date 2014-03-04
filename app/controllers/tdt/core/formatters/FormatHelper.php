<?php

namespace tdt\core\formatters;



/**
 * FormatHelper helps finding available formats for a certain datastructure
 *
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Jan Vansteenlandt <jan@okfn.be>
 */
class FormatHelper{

    private static $tabular_sources = array('csv', 'xls', 'shp');

    /**
     * Return a list of the available formats that the data structure can be formatted into
     *
     * @param tdt\core\datasets\Data $data
     * @return array
     */
    public function getAvailableFormats($data){

        $formats = array(
            array('php' => 'php'),
            array('json' => 'json'),
            array('xml' => 'xml'),
        );

        // Check for tabular sources
        if(in_array(strtolower($data->source_definition['type']), self::$tabular_sources)){
            array_push($formats, array('csv' => 'csv'));
        }

        // Check for semantic sources, identified by the data being wrapped in an EasyRdf_Graph
        if(is_object($data->data) && get_class($data->data) == 'EasyRdf_Graph'){

            array_push($formats, array('json-ld' => 'jsonld'));
            array_push($formats, array('ntriples' => 'nt'));
            array_push($formats, array('turtle' => 'ttl'));
        }

        // Check for geographical properties
        if(!empty($data->geo)){
            array_push($formats, array('map' => 'map'));
        }

        return $formats;
    }
}